<?php

namespace App\Controllers;

class TopupController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * List topup requests for current user
     */
    public function index()
    {
        $userId  = session()->get('user_id');
        $perPage = 15;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total = $this->db->table('topup_requests')
            ->where('user_id', $userId)
            ->countAllResults();

        $requests = $this->db->table('topup_requests')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = \Config\Services::pager();

        $data = [
            'title'    => 'Yêu cầu nạp tiền',
            'topups'   => $requests,
            'pager'    => $pager->makeLinks($page, $perPage, $total),
            'total'    => $total,
        ];

        return view('topup/index', $data);
    }

    /**
     * Create topup request
     */
    public function create()
    {
        // Get system bank accounts for the form
        $systemBanks = $this->db->table('system_bank_accounts')
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'amount'           => 'required|decimal|greater_than[0]',
                'bank_name'        => 'permit_empty|max_length[100]',
                'transfer_content' => 'permit_empty|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return view('topup/create', [
                    'validation'  => $this->validator,
                    'systemBanks' => $systemBanks,
                ]);
            }

            $userId = session()->get('user_id');
            $code   = 'NAP' . date('Ymd') . rand(1000, 9999);

            // Ensure unique code
            while ($this->db->table('topup_requests')->where('code', $code)->countAllResults() > 0) {
                $code = 'NAP' . date('Ymd') . rand(1000, 9999);
            }

            // Handle receipt image upload
            $receiptImage = null;
            $file = $this->request->getFile('receipt_image');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = WRITEPATH . 'uploads/receipts/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $newName = $code . '_' . $file->getRandomName();
                $file->move($uploadPath, $newName);
                $receiptImage = 'receipts/' . $newName;
            }

            $this->db->table('topup_requests')->insert([
                'user_id'          => $userId,
                'code'             => $code,
                'amount'           => $this->request->getPost('amount'),
                'bank_name'        => $this->request->getPost('bank_name'),
                'transfer_content' => $this->request->getPost('transfer_content') ?: $code,
                'receipt_image'    => $receiptImage,
                'status'           => 'pending',
            ]);

            $requestId = $this->db->insertID();

            return redirect()->to('/topup/' . $requestId)->with('success', 'Yêu cầu nạp tiền đã được tạo. Mã: ' . $code);
        }

        return view('topup/create', [
            'title'       => 'Tạo yêu cầu nạp tiền',
            'systemBanks' => $systemBanks,
        ]);
    }

    /**
     * Show topup detail
     */
    public function show($id)
    {
        $userId = session()->get('user_id');

        $request = $this->db->table('topup_requests')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$request) {
            return redirect()->to('/topup')->with('error', 'Yêu cầu nạp tiền không tồn tại.');
        }

        $data = [
            'title'   => 'Chi tiết nạp tiền ' . $request['code'],
            'topup'   => $request,
        ];

        return view('topup/show', $data);
    }
}
