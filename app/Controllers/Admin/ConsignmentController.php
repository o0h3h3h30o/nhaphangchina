<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ConsignmentOrderModel;
use App\Models\ConsignmentStatusHistoryModel;
use App\Models\TrackingEventModel;
use App\Models\ShippingRateModel;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;

class ConsignmentController extends BaseController
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

        $orderModel = new ConsignmentOrderModel();
        $builder    = $orderModel->builder();
        $builder->select('consignment_orders.*, users.username')
            ->join('users', 'users.id = consignment_orders.user_id', 'left');

        // Search
        $search = $this->request->getGet('search');
        if ($search) {
            // Support HP-id search
            if (preg_match('/^HP(\d+)$/i', $search, $m)) {
                $builder->where('consignment_orders.user_id', (int)$m[1]);
            } else {
                $builder->groupStart()
                    ->like('order_code', $search)
                    ->orLike('cn_tracking_code', $search)
                    ->orLike('product_name', $search)
                    ->orLike('vn_receiver_name', $search)
                    ->orLike('vn_receiver_phone', $search)
                    ->orLike('users.username', $search)
                    ->groupEnd();
            }
        }

        // Filter by status
        $status = $this->request->getGet('status');
        if ($status === 'orphan') {
            $builder->groupStart()
                ->where('consignment_orders.user_id IS NULL')
                ->orWhere('consignment_orders.user_id', 0)
                ->groupEnd();
        } elseif ($status) {
            $builder->where('consignment_orders.status', $status);
            // Khi lọc trạng thái cụ thể, bỏ đơn vô danh
            $builder->where('consignment_orders.user_id IS NOT NULL')
                ->where('consignment_orders.user_id !=', 0);
        }

        // Filter by cargo_type
        $cargoType = $this->request->getGet('cargo_type');
        if ($cargoType) {
            $builder->where('cargo_type', $cargoType);
        }

        $perPage = 20;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total  = $builder->countAllResults(false);
        $orders = $builder->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'title'     => 'Quản lý đơn ký gửi',
            'orders'    => $orders,
            'pager'     => $pager,
            'search'    => $search,
            'status'    => $status,
            'cargoType' => $cargoType,
            'total'     => $total,
            'page'      => $page,
            'perPage'   => $perPage,
        ];

        return view('admin/consignments/index', $data);
    }

    /**
     * AJAX lookup order by cn_tracking_code
     */
    public function lookup()
    {
        if ($redirect = $this->checkAdmin()) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $trackingCode = trim($this->request->getPost('tracking_code') ?? '');
        if (!$trackingCode) {
            return $this->response->setJSON(['error' => 'Vui lòng nhập mã vận đơn.']);
        }

        $db = \Config\Database::connect();
        $order = $db->table('consignment_orders')
            ->select('consignment_orders.*, users.username')
            ->join('users', 'users.id = consignment_orders.user_id', 'left')
            ->where('cn_tracking_code', $trackingCode)
            ->get()
            ->getRowArray();

        if (!$order) {
            return $this->response->setJSON(['error' => 'Không tìm thấy đơn với mã vận đơn: ' . $trackingCode]);
        }

        return $this->response->setJSON(['success' => true, 'order' => $order]);
    }

    public function show($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $orderModel   = new ConsignmentOrderModel();
        $historyModel = new ConsignmentStatusHistoryModel();
        $eventModel   = new TrackingEventModel();

        $db = \Config\Database::connect();
        $order = $db->table('consignment_orders')
            ->select('consignment_orders.*, users.username, users.email as user_email, users.phone as user_phone')
            ->join('users', 'users.id = consignment_orders.user_id', 'left')
            ->where('consignment_orders.id', $id)
            ->get()
            ->getRowArray();
        if (!$order) {
            return redirect()->to('/admin/consignments')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $statusHistory = $historyModel->where('consignment_order_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $trackingEvents = $eventModel->where('consignment_order_id', $id)
            ->orderBy('event_at', 'DESC')
            ->findAll();

        $data = [
            'title'          => 'Chi tiết đơn ký gửi',
            'order'          => $order,
            'statusHistory'  => $statusHistory,
            'trackingEvents' => $trackingEvents,
        ];

        return view('admin/consignments/show', $data);
    }

    public function updateStatus($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $orderModel   = new ConsignmentOrderModel();
        $historyModel = new ConsignmentStatusHistoryModel();
        $eventModel   = new TrackingEventModel();

        $order = $orderModel->find($id);
        if (!$order) {
            return redirect()->to('/admin/consignments')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $newStatus = $this->request->getPost('status');
        $note      = $this->request->getPost('note') ?? '';
        $oldStatus = $order['status'];

        if ($oldStatus === $newStatus) {
            return redirect()->to("/admin/consignments/{$id}")->with('error', 'Trạng thái không thay đổi.');
        }

        $updateData = ['status' => $newStatus];

        // When status changes to received_vn, allow weight input
        if ($newStatus === 'received_vn') {
            $weight = $this->request->getPost('actual_weight');
            if ($weight && $weight > 0) {
                $updateData['actual_weight'] = (float) $weight;
            }
        }

        $orderModel->update($id, $updateData);

        // Create status history
        $historyModel->insert([
            'consignment_order_id' => $id,
            'from_status'          => $oldStatus,
            'to_status'            => $newStatus,
            'note'                 => $note,
            'changed_by'           => $this->session->get('user_id'),
            'created_at'           => date('Y-m-d H:i:s'),
        ]);

        // Create tracking event
        $eventModel->insert([
            'consignment_order_id' => $id,
            'event_type'           => 'status_change',
            'title'                => "Trạng thái: {$newStatus}",
            'description'          => $note ?: "Cập nhật trạng thái từ {$oldStatus} sang {$newStatus}",
            'handler'              => $this->session->get('username') ?? 'admin',
            'created_by'           => $this->session->get('user_id'),
            'event_at'             => date('Y-m-d H:i:s'),
            'created_at'           => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/admin/consignments/{$id}")->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function updateWeight($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $orderModel = new ConsignmentOrderModel();
        $historyModel = new ConsignmentStatusHistoryModel();

        $order = $orderModel->find($id);
        if (!$order) {
            return redirect()->to('/admin/consignments')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $actualWeight = (float) $this->request->getPost('actual_weight');
        if ($actualWeight <= 0) {
            return redirect()->to("/admin/consignments/{$id}")->with('error', 'Cân nặng không hợp lệ.');
        }

        // Get user's group
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $order['user_id'])->get()->getRowArray();
        $userGroupId = $user['user_group_id'] ?? null;

        // Nếu user chưa gán nhóm, lấy nhóm mặc định
        if (!$userGroupId) {
            $defaultGroup = $db->table('user_groups')->where('is_default', 1)->get()->getRowArray();
            $userGroupId = $defaultGroup['id'] ?? null;
        }

        // Get active shipping rate - ưu tiên giá theo nhóm user
        $route = $order['cn_warehouse'] ?: 'CN-VN';
        $cargoType = $order['cargo_type'] ?: 'general';

        // Hàm tìm rate theo route + cargo_type, thử nhiều fallback
        $findRate = function($groupCondition) use ($db, $route, $cargoType) {
            $builder = $db->table('shipping_rates')
                ->where('is_active', 1)
                ->where('effective_from <=', date('Y-m-d'));

            if ($groupCondition === null) {
                $builder->where('user_group_id IS NULL');
            } else {
                $builder->where('user_group_id', $groupCondition);
            }

            // 1. Tìm chính xác route + cargo_type
            $rate = (clone $builder)->where('route', $route)->where('cargo_type', $cargoType)
                ->orderBy('effective_from', 'DESC')->get()->getRowArray();
            if ($rate) return $rate;

            // 2. Fallback cargo_type = general
            if ($cargoType !== 'general') {
                $rate = (clone $builder)->where('route', $route)->where('cargo_type', 'general')
                    ->orderBy('effective_from', 'DESC')->get()->getRowArray();
                if ($rate) return $rate;
            }

            // 3. Fallback: bất kỳ rate nào của route đó
            $rate = (clone $builder)->where('route', $route)
                ->orderBy('effective_from', 'DESC')->get()->getRowArray();
            if ($rate) return $rate;

            // 4. Fallback: bất kỳ rate nào
            return (clone $builder)->orderBy('effective_from', 'DESC')->get()->getRowArray();
        };

        $rate = null;
        if ($userGroupId) {
            $rate = $findRate($userGroupId);
        }
        if (!$rate) {
            $rate = $findRate(null);
        }

        if (!$rate) {
            return redirect()->to("/admin/consignments/{$id}")->with('error', 'Không tìm thấy bảng giá phù hợp. Vui lòng tạo bảng giá trước.');
        }

        // Calculate rounded weight
        $roundedWeight = $actualWeight;
        switch ($rate['rounding_method'] ?? '0.5kg') {
            case '0.5kg':
                $roundedWeight = ceil($actualWeight * 2) / 2;
                break;
            case '1kg':
                $roundedWeight = ceil($actualWeight);
                break;
            case 'actual':
                $roundedWeight = $actualWeight;
                break;
        }

        // Apply minimum weight
        $roundedWeight = max($roundedWeight, (float) ($rate['min_weight'] ?? 0));

        // Calculate fees
        $shippingFee = $roundedWeight * (float) $rate['rate_per_kg'];

        $extraFee = 0;
        $cargoType = $order['cargo_type'] ?? 'normal';
        if ($cargoType === 'fragile') {
            $extraFee = (float) ($rate['extra_fee_fragile'] ?? 0);
        } elseif ($cargoType === 'bulky') {
            $extraFee = (float) ($rate['extra_fee_bulky'] ?? 0);
        } elseif ($cargoType === 'special') {
            $extraFee = (float) ($rate['extra_fee_special'] ?? 0);
        }

        $serviceFee = (float) ($order['service_fee'] ?? 0);
        $totalFee   = $shippingFee + $extraFee + $serviceFee;

        $oldStatus = $order['status'];

        $orderModel->update($id, [
            'actual_weight' => $actualWeight,
            'shipping_fee'  => $shippingFee,
            'extra_fee'     => $extraFee,
            'total_fee'     => $totalFee,
            'status'        => 'fee_calculated',
            'fee_snapshot'  => json_encode([
                'rate_id'          => $rate['id'],
                'user_group_id'    => $rate['user_group_id'] ?? null,
                'rate_per_kg'      => $rate['rate_per_kg'],
                'rounding_method'  => $rate['rounding_method'] ?? '0.5kg',
                'rounded_weight'   => $roundedWeight,
                'actual_weight'    => $actualWeight,
                'min_weight'       => $rate['min_weight'] ?? 0,
                'shipping_fee'     => $shippingFee,
                'extra_fee'        => $extraFee,
                'service_fee'      => $serviceFee,
                'total_fee'        => $totalFee,
                'calculated_at'    => date('Y-m-d H:i:s'),
            ]),
        ]);

        // Create status history
        $historyModel->insert([
            'consignment_order_id' => $id,
            'from_status'          => $oldStatus,
            'to_status'            => 'fee_calculated',
            'note'                 => "Cân nặng: {$actualWeight}kg, Phí: {$totalFee}",
            'changed_by'           => $this->session->get('user_id'),
            'created_at'           => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/admin/consignments/{$id}")->with('success', "Cập nhật cân nặng và tính phí thành công. Tổng phí: " . number_format($totalFee) . " VNĐ");
    }

    public function calculateFee($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $orderModel  = new ConsignmentOrderModel();
        $walletModel = new WalletModel();
        $txModel     = new WalletTransactionModel();
        $historyModel = new ConsignmentStatusHistoryModel();

        $order = $orderModel->find($id);
        if (!$order) {
            return redirect()->to('/admin/consignments')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $totalFee = (float) ($order['total_fee'] ?? 0);
        if ($totalFee <= 0) {
            return redirect()->to("/admin/consignments/{$id}")->with('error', 'Chưa tính phí cho đơn hàng này.');
        }

        if ($order['paid']) {
            return redirect()->to("/admin/consignments/{$id}")->with('error', 'Đơn hàng đã được thanh toán.');
        }

        $wallet = $walletModel->where('user_id', $order['user_id'])->first();
        if (!$wallet) {
            return redirect()->to("/admin/consignments/{$id}")->with('error', 'Người dùng chưa có ví.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Lock wallet row for update
        $lockedWallet = $db->query(
            "SELECT * FROM wallets WHERE id = ? FOR UPDATE",
            [$wallet['id']]
        )->getRowArray();

        $balance = (float) $lockedWallet['balance'];
        $oldStatus = $order['status'];

        if ($balance >= $totalFee) {
            // Sufficient balance: deduct and mark paid
            $newBalance = $balance - $totalFee;

            $db->query("UPDATE wallets SET balance = ?, updated_at = ? WHERE id = ?", [
                $newBalance,
                date('Y-m-d H:i:s'),
                $wallet['id'],
            ]);

            $txModel->insert([
                'wallet_id'      => $wallet['id'],
                'user_id'        => $order['user_id'],
                'type'           => 'deduct',
                'amount'         => $totalFee,
                'balance_before' => $balance,
                'balance_after'  => $newBalance,
                'reference_type' => 'consignment_order',
                'reference_id'   => $id,
                'description'    => "Thanh toán đơn ký gửi #{$order['order_code']}",
                'created_by'     => $this->session->get('user_id'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            $orderModel->update($id, [
                'paid'    => 1,
                'paid_at' => date('Y-m-d H:i:s'),
                'status'  => 'paid',
            ]);

            $historyModel->insert([
                'consignment_order_id' => $id,
                'from_status'          => $oldStatus,
                'to_status'            => 'paid',
                'note'                 => "Trừ ví thành công: " . number_format($totalFee) . " VNĐ",
                'changed_by'           => $this->session->get('user_id'),
                'created_at'           => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to("/admin/consignments/{$id}")->with('error', 'Lỗi giao dịch, vui lòng thử lại.');
            }

            return redirect()->to("/admin/consignments/{$id}")->with('success', 'Thanh toán thành công.');
        } else {
            // Insufficient balance
            $orderModel->update($id, ['status' => 'waiting_payment']);

            $historyModel->insert([
                'consignment_order_id' => $id,
                'from_status'          => $oldStatus,
                'to_status'            => 'waiting_payment',
                'note'                 => "Số dư không đủ. Cần: " . number_format($totalFee) . ", Có: " . number_format($balance),
                'changed_by'           => $this->session->get('user_id'),
                'created_at'           => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            return redirect()->to("/admin/consignments/{$id}")->with('error', 'Số dư ví không đủ. Đơn hàng chuyển sang chờ thanh toán.');
        }
    }
}
