<?php

namespace App\Controllers;

class WalletController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Show wallet overview: balance, locked, available, recent transactions
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Get wallet
        $wallet = $this->db->table('wallets')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$wallet) {
            // Create wallet if not exists
            $this->db->table('wallets')->insert([
                'user_id'        => $userId,
                'balance'        => 0.00,
                'locked_balance' => 0.00,
            ]);
            $wallet = [
                'id'             => $this->db->insertID(),
                'balance'        => 0.00,
                'locked_balance' => 0.00,
            ];
        }

        $balance          = (float) $wallet['balance'];
        $lockedBalance    = (float) $wallet['locked_balance'];
        $availableBalance = $balance - $lockedBalance;

        // Recent transactions (paginated)
        $perPage = 10;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total = $this->db->table('wallet_transactions')
            ->where('user_id', $userId)
            ->countAllResults();

        $transactions = $this->db->table('wallet_transactions')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = \Config\Services::pager();

        $data = [
            'title'            => 'Ví tiền',
            'wallet'           => $wallet,
            'balance'          => $balance,
            'lockedBalance'    => $lockedBalance,
            'availableBalance' => $availableBalance,
            'transactions'     => $transactions,
            'pager'            => $pager->makeLinks($page, $perPage, $total),
            'total'            => $total,
        ];

        return view('wallet/index', $data);
    }

    /**
     * Full transaction history with search/filter
     */
    public function transactions()
    {
        $userId  = session()->get('user_id');
        $perPage = 20;

        $builder = $this->db->table('wallet_transactions')
            ->where('user_id', $userId);

        // Filter by type
        $type = $this->request->getGet('type');
        if ($type) {
            $builder->where('type', $type);
        }

        // Search by description
        $search = $this->request->getGet('search');
        if ($search) {
            $builder->like('description', $search);
        }

        // Date range
        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to');
        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        $total  = $builder->countAllResults(false);
        $page   = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        $transactions = $builder->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = \Config\Services::pager();

        $data = [
            'title'        => 'Lịch sử giao dịch',
            'transactions' => $transactions,
            'pager'        => $pager->makeLinks($page, $perPage, $total),
            'total'        => $total,
            'type'         => $type,
            'search'       => $search,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
        ];

        return view('wallet/transactions', $data);
    }
}
