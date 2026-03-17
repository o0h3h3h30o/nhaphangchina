<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TopupRequestModel;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;

class TopupController extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = service('session');
    }

    private function checkAdmin()
    {
        $role = $this->session->get('user_role');
        if (!in_array($role, ['admin', 'staff'])) {
            return redirect()->to('/auth/login')->with('error', 'Bạn không có quyền truy cập.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('topup_requests t')
            ->select('t.*, u.username')
            ->join('users u', 'u.id = t.user_id', 'left');

        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('t.status', $status);
        }

        $perPage = 20;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total  = $builder->countAllResults(false);
        $topups = $builder->orderBy('t.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'title'   => 'Quản lý nạp tiền',
            'topups'  => $topups,
            'pager'   => $pager,
            'status'  => $status,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ];

        return view('admin/topups/index', $data);
    }

    public function show($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $topup = $db->table('topup_requests t')
            ->select('t.*, u.username, u.email as user_email, u.phone as user_phone')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->where('t.id', $id)
            ->get()
            ->getRowArray();

        if (!$topup) {
            return redirect()->to('/admin/topups')->with('error', 'Không tìm thấy yêu cầu nạp tiền.');
        }

        return view('admin/topups/show', [
            'title' => 'Chi tiết yêu cầu nạp tiền',
            'topup' => $topup,
        ]);
    }

    public function approve($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $topupModel  = new TopupRequestModel();
        $walletModel = new WalletModel();
        $txModel     = new WalletTransactionModel();

        $topup = $topupModel->find($id);
        if (!$topup) {
            return redirect()->to('/admin/topups')->with('error', 'Không tìm thấy yêu cầu nạp tiền.');
        }

        if ($topup['status'] !== 'pending') {
            return redirect()->to("/admin/topups/{$id}")->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $wallet = $walletModel->where('user_id', $topup['user_id'])->first();
        if (!$wallet) {
            return redirect()->to("/admin/topups/{$id}")->with('error', 'Người dùng chưa có ví.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Lock wallet
        $lockedWallet = $db->query(
            "SELECT * FROM wallets WHERE id = ? FOR UPDATE",
            [$wallet['id']]
        )->getRowArray();

        $balanceBefore = (float) $lockedWallet['balance'];
        $amount        = (float) $topup['amount'];
        $balanceAfter  = $balanceBefore + $amount;

        $db->query("UPDATE wallets SET balance = ?, updated_at = ? WHERE id = ?", [
            $balanceAfter,
            date('Y-m-d H:i:s'),
            $wallet['id'],
        ]);

        $txModel->insert([
            'wallet_id'      => $wallet['id'],
            'user_id'        => $topup['user_id'],
            'type'           => 'topup',
            'amount'         => $amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceAfter,
            'reference_type' => 'topup_request',
            'reference_id'   => $id,
            'description'    => "Nạp tiền #{$topup['code']}",
            'created_by'     => $this->session->get('user_id'),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $topupModel->update($id, [
            'status'      => 'approved',
            'approved_by' => $this->session->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to("/admin/topups/{$id}")->with('error', 'Lỗi giao dịch, vui lòng thử lại.');
        }

        return redirect()->to("/admin/topups/{$id}")->with('success', 'Đã duyệt nạp tiền thành công.');
    }

    public function reject($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $topupModel = new TopupRequestModel();
        $topup      = $topupModel->find($id);

        if (!$topup) {
            return redirect()->to('/admin/topups')->with('error', 'Không tìm thấy yêu cầu nạp tiền.');
        }

        if ($topup['status'] !== 'pending') {
            return redirect()->to("/admin/topups/{$id}")->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $reason = $this->request->getPost('reject_reason') ?? '';

        $topupModel->update($id, [
            'status'        => 'rejected',
            'reject_reason' => $reason,
            'approved_by'   => $this->session->get('user_id'),
            'approved_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/admin/topups/{$id}")->with('success', 'Đã từ chối yêu cầu nạp tiền.');
    }
}
