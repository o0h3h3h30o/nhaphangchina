<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * User dashboard with stats
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Total orders
        $totalOrders = $this->db->table('consignment_orders')
            ->where('user_id', $userId)
            ->countAllResults();

        // Pending orders (not completed or cancelled)
        $pendingOrders = $this->db->table('consignment_orders')
            ->where('user_id', $userId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->countAllResults();

        // Wallet balance
        $wallet = $this->db->table('wallets')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        $walletBalance = $wallet ? (float) $wallet['balance'] : 0;
        $lockedBalance = $wallet ? (float) $wallet['locked_balance'] : 0;

        // Completed orders
        $completedOrders = $this->db->table('consignment_orders')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->countAllResults();

        // Recent orders (last 5)
        $recentOrders = $this->db->table('consignment_orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $data = [
            'title'           => 'Dashboard',
            'totalOrders'     => $totalOrders,
            'pendingOrders'   => $pendingOrders,
            'completedOrders' => $completedOrders,
            'walletBalance'   => $walletBalance,
            'lockedBalance'   => $lockedBalance,
            'recentOrders'    => $recentOrders,
        ];

        return view('dashboard/index', $data);
    }
}
