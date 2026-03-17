<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WithdrawalRequestModel;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;
use App\Models\BankAccountModel;

class WithdrawalController extends BaseController
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

        $model   = new WithdrawalRequestModel();
        $builder = $model->builder();

        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('status', $status);
        }

        $perPage = 20;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total       = $builder->countAllResults(false);
        $withdrawals = $builder->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'title'       => 'Quản lý rút tiền',
            'withdrawals' => $withdrawals,
            'pager'       => $pager,
            'status'      => $status,
            'total'       => $total,
            'page'        => $page,
            'perPage'     => $perPage,
        ];

        return view('admin/withdrawals/index', $data);
    }

    public function show($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $model      = new WithdrawalRequestModel();
        $bankModel  = new BankAccountModel();
        $withdrawal = $model->find($id);

        if (!$withdrawal) {
            return redirect()->to('/admin/withdrawals')->with('error', 'Không tìm thấy yêu cầu rút tiền.');
        }

        $bankAccount = null;
        if (!empty($withdrawal['bank_account_id'])) {
            $bankAccount = $bankModel->find($withdrawal['bank_account_id']);
        }

        $data = [
            'title'       => 'Chi tiết yêu cầu rút tiền',
            'withdrawal'  => $withdrawal,
            'bankAccount' => $bankAccount,
        ];

        return view('admin/withdrawals/show', $data);
    }

    public function approve($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $model      = new WithdrawalRequestModel();
        $withdrawal = $model->find($id);

        if (!$withdrawal) {
            return redirect()->to('/admin/withdrawals')->with('error', 'Không tìm thấy yêu cầu rút tiền.');
        }

        if ($withdrawal['status'] !== 'pending') {
            return redirect()->to("/admin/withdrawals/{$id}")->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $model->update($id, [
            'status'      => 'approved',
            'approved_by' => $this->session->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/admin/withdrawals/{$id}")->with('success', 'Đã duyệt yêu cầu rút tiền.');
    }

    public function complete($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $model      = new WithdrawalRequestModel();
        $withdrawal = $model->find($id);

        if (!$withdrawal) {
            return redirect()->to('/admin/withdrawals')->with('error', 'Không tìm thấy yêu cầu rút tiền.');
        }

        if ($withdrawal['status'] !== 'approved') {
            return redirect()->to("/admin/withdrawals/{$id}")->with('error', 'Yêu cầu chưa được duyệt.');
        }

        $model->update($id, [
            'status'       => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/admin/withdrawals/{$id}")->with('success', 'Đã hoàn tất chuyển tiền.');
    }

    public function reject($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $model      = new WithdrawalRequestModel();
        $walletModel = new WalletModel();
        $txModel     = new WalletTransactionModel();

        $withdrawal = $model->find($id);
        if (!$withdrawal) {
            return redirect()->to('/admin/withdrawals')->with('error', 'Không tìm thấy yêu cầu rút tiền.');
        }

        if ($withdrawal['status'] !== 'pending') {
            return redirect()->to("/admin/withdrawals/{$id}")->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $reason = $this->request->getPost('reject_reason') ?? '';
        $amount = (float) $withdrawal['amount'];

        $wallet = $walletModel->where('user_id', $withdrawal['user_id'])->first();
        if (!$wallet) {
            return redirect()->to("/admin/withdrawals/{$id}")->with('error', 'Không tìm thấy ví.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Lock wallet
        $lockedWallet = $db->query(
            "SELECT * FROM wallets WHERE id = ? FOR UPDATE",
            [$wallet['id']]
        )->getRowArray();

        $balanceBefore = (float) $lockedWallet['balance'];
        $lockedBalance = (float) $lockedWallet['locked_balance'];
        $balanceAfter  = $balanceBefore + $amount;
        $newLocked     = max(0, $lockedBalance - $amount);

        // Unlock the amount back to available balance
        $db->query("UPDATE wallets SET balance = ?, locked_balance = ?, updated_at = ? WHERE id = ?", [
            $balanceAfter,
            $newLocked,
            date('Y-m-d H:i:s'),
            $wallet['id'],
        ]);

        $txModel->insert([
            'wallet_id'      => $wallet['id'],
            'user_id'        => $withdrawal['user_id'],
            'type'           => 'refund',
            'amount'         => $amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceAfter,
            'reference_type' => 'withdrawal_request',
            'reference_id'   => $id,
            'description'    => "Hoàn tiền rút #{$withdrawal['code']}: {$reason}",
            'created_by'     => $this->session->get('user_id'),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $model->update($id, [
            'status'        => 'rejected',
            'reject_reason' => $reason,
            'approved_by'   => $this->session->get('user_id'),
            'approved_at'   => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to("/admin/withdrawals/{$id}")->with('error', 'Lỗi giao dịch, vui lòng thử lại.');
        }

        return redirect()->to("/admin/withdrawals/{$id}")->with('success', 'Đã từ chối và hoàn tiền.');
    }
}
