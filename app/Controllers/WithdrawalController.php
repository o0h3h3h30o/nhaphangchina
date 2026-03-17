<?php

namespace App\Controllers;

class WithdrawalController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * List withdrawal requests for current user
     */
    public function index()
    {
        $userId  = session()->get('user_id');
        $perPage = 15;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total = $this->db->table('withdrawal_requests')
            ->where('user_id', $userId)
            ->countAllResults();

        $requests = $this->db->table('withdrawal_requests as wr')
            ->select('wr.*, uba.bank_name, uba.account_number, uba.account_holder')
            ->join('user_bank_accounts uba', 'uba.id = wr.bank_account_id', 'left')
            ->where('wr.user_id', $userId)
            ->orderBy('wr.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = \Config\Services::pager();

        $data = [
            'title'    => 'Yêu cầu rút tiền',
            'requests' => $requests,
            'pager'    => $pager->makeLinks($page, $perPage, $total),
            'total'    => $total,
        ];

        return view('withdrawal/index', $data);
    }

    /**
     * Create withdrawal request
     */
    public function create()
    {
        $userId = session()->get('user_id');

        // Get user's bank accounts
        $bankAccounts = $this->db->table('user_bank_accounts')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();

        // Get wallet info
        $wallet = $this->db->table('wallets')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        $availableBalance = $wallet ? ((float) $wallet['balance'] - (float) $wallet['locked_balance']) : 0;

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'bank_account_id' => 'required|integer',
                'amount'          => 'required|decimal|greater_than[0]',
            ];

            if (!$this->validate($rules)) {
                return view('withdrawal/create', [
                    'validation'       => $this->validator,
                    'bankAccounts'     => $bankAccounts,
                    'availableBalance' => $availableBalance,
                ]);
            }

            $amount        = (float) $this->request->getPost('amount');
            $bankAccountId = (int) $this->request->getPost('bank_account_id');

            // Validate balance
            if ($amount > $availableBalance) {
                return view('withdrawal/create', [
                    'error'            => 'Số dư khả dụng không đủ.',
                    'bankAccounts'     => $bankAccounts,
                    'availableBalance' => $availableBalance,
                ]);
            }

            // Validate bank account ownership
            $bankAccount = $this->db->table('user_bank_accounts')
                ->where('id', $bankAccountId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            if (!$bankAccount) {
                return view('withdrawal/create', [
                    'error'            => 'Tài khoản ngân hàng không hợp lệ.',
                    'bankAccounts'     => $bankAccounts,
                    'availableBalance' => $availableBalance,
                ]);
            }

            $code = 'RUT' . date('Ymd') . rand(1000, 9999);

            // Ensure unique code
            while ($this->db->table('withdrawal_requests')->where('code', $code)->countAllResults() > 0) {
                $code = 'RUT' . date('Ymd') . rand(1000, 9999);
            }

            $this->db->transStart();

            // Create withdrawal request
            $this->db->table('withdrawal_requests')->insert([
                'user_id'         => $userId,
                'code'            => $code,
                'bank_account_id' => $bankAccountId,
                'amount'          => $amount,
                'status'          => 'pending',
            ]);

            // Lock the amount in wallet
            $this->db->table('wallets')
                ->where('user_id', $userId)
                ->set('locked_balance', 'locked_balance + ' . $amount, false)
                ->update();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Đã xảy ra lỗi khi tạo yêu cầu rút tiền.');
            }

            $requestId = $this->db->insertID();

            return redirect()->to('/withdrawal/' . $requestId)->with('success', 'Yêu cầu rút tiền đã được tạo. Mã: ' . $code);
        }

        return view('withdrawal/create', [
            'title'            => 'Tạo yêu cầu rút tiền',
            'bankAccounts'     => $bankAccounts,
            'availableBalance' => $availableBalance,
        ]);
    }

    /**
     * Show withdrawal detail
     */
    public function show($id)
    {
        $userId = session()->get('user_id');

        $request = $this->db->table('withdrawal_requests as wr')
            ->select('wr.*, uba.bank_name, uba.account_number, uba.account_holder, uba.branch')
            ->join('user_bank_accounts uba', 'uba.id = wr.bank_account_id', 'left')
            ->where('wr.id', $id)
            ->where('wr.user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$request) {
            return redirect()->to('/withdrawal')->with('error', 'Yêu cầu rút tiền không tồn tại.');
        }

        $data = [
            'title'   => 'Chi tiết rút tiền ' . $request['code'],
            'request' => $request,
        ];

        return view('withdrawal/show', $data);
    }
}
