<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ConsignmentOrderModel;
use App\Models\TopupRequestModel;
use App\Models\WithdrawalRequestModel;
use App\Models\WalletTransactionModel;

class DashboardController extends BaseController
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

        $userModel        = new UserModel();
        $orderModel       = new ConsignmentOrderModel();
        $topupModel       = new TopupRequestModel();
        $withdrawalModel  = new WithdrawalRequestModel();
        $transactionModel = new WalletTransactionModel();

        // Total users
        $totalUsers = $userModel->countAllResults(false);

        // Active orders by status
        $activeStatuses = [
            'pending', 'confirmed', 'received_cn', 'packed_for_truck',
            'in_transit_cn_vn', 'received_vn', 'ready_for_delivery', 'out_for_delivery',
        ];
        $activeOrders = $orderModel->whereIn('status', $activeStatuses)->countAllResults(false);

        // Pending topups
        $pendingTopups = $topupModel->where('status', 'pending')->countAllResults(false);

        // Pending withdrawals
        $pendingWithdrawals = $withdrawalModel->where('status', 'pending')->countAllResults(false);

        // Today's transactions total
        $today = date('Y-m-d');
        $todayTransactions = $transactionModel
            ->selectSum('amount')
            ->where('DATE(created_at)', $today)
            ->first();
        $todayTotal = $todayTransactions['amount'] ?? 0;

        // Orders by status counts
        $ordersByStatus = [];
        $allStatuses = [
            'pending', 'confirmed', 'received_cn', 'packed_for_truck',
            'in_transit_cn_vn', 'received_vn', 'fee_calculated', 'waiting_payment',
            'paid', 'ready_for_delivery', 'out_for_delivery', 'delivered',
            'completed', 'cancelled',
        ];
        foreach ($allStatuses as $status) {
            $ordersByStatus[$status] = $orderModel->where('status', $status)->countAllResults(false);
        }

        $data = [
            'title'              => 'Admin Dashboard',
            'totalUsers'         => $totalUsers,
            'activeOrders'       => $activeOrders,
            'pendingTopups'      => $pendingTopups,
            'pendingWithdrawals' => $pendingWithdrawals,
            'todayTotal'         => $todayTotal,
            'ordersByStatus'     => $ordersByStatus,
        ];

        return view('admin/dashboard/index', $data);
    }
}
