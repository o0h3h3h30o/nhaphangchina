<?php

namespace App\Controllers;

class BankAccountController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * List user's bank accounts
     */
    public function index()
    {
        $userId = session()->get('user_id');

        $accounts = $this->db->table('user_bank_accounts')
            ->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title'    => 'Tài khoản ngân hàng',
            'accounts' => $accounts,
        ];

        return view('bank_accounts/index', $data);
    }

    /**
     * Create bank account
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'bank_name'      => 'required|max_length[100]',
                'account_number' => 'required|max_length[50]',
                'account_holder' => 'required|max_length[100]',
                'branch'         => 'permit_empty|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return view('bank_accounts/create', [
                    'validation' => $this->validator,
                ]);
            }

            $userId = session()->get('user_id');

            // Check if this is the first bank account (set as default)
            $existingCount = $this->db->table('user_bank_accounts')
                ->where('user_id', $userId)
                ->countAllResults();

            $this->db->table('user_bank_accounts')->insert([
                'user_id'        => $userId,
                'bank_name'      => $this->request->getPost('bank_name'),
                'account_number' => $this->request->getPost('account_number'),
                'account_holder' => $this->request->getPost('account_holder'),
                'branch'         => $this->request->getPost('branch'),
                'is_default'     => $existingCount === 0 ? 1 : 0,
            ]);

            return redirect()->to('/bank-account')->with('success', 'Thêm tài khoản ngân hàng thành công.');
        }

        return view('bank_accounts/create', [
            'title' => 'Thêm tài khoản ngân hàng',
        ]);
    }

    /**
     * Edit bank account
     */
    public function edit($id)
    {
        $userId = session()->get('user_id');

        $account = $this->db->table('user_bank_accounts')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$account) {
            return redirect()->to('/bank-account')->with('error', 'Tài khoản ngân hàng không tồn tại.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'bank_name'      => 'required|max_length[100]',
                'account_number' => 'required|max_length[50]',
                'account_holder' => 'required|max_length[100]',
                'branch'         => 'permit_empty|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return view('bank_accounts/edit', [
                    'validation' => $this->validator,
                    'account'    => $account,
                ]);
            }

            $this->db->table('user_bank_accounts')
                ->where('id', $id)
                ->update([
                    'bank_name'      => $this->request->getPost('bank_name'),
                    'account_number' => $this->request->getPost('account_number'),
                    'account_holder' => $this->request->getPost('account_holder'),
                    'branch'         => $this->request->getPost('branch'),
                ]);

            return redirect()->to('/bank-account')->with('success', 'Cập nhật tài khoản ngân hàng thành công.');
        }

        return view('bank_accounts/edit', [
            'title'   => 'Chỉnh sửa tài khoản ngân hàng',
            'account' => $account,
        ]);
    }

    /**
     * Delete bank account
     */
    public function delete($id)
    {
        $userId = session()->get('user_id');

        $account = $this->db->table('user_bank_accounts')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$account) {
            return redirect()->to('/bank-account')->with('error', 'Tài khoản ngân hàng không tồn tại.');
        }

        // Prevent deletion if it's the only default account
        if ($account['is_default']) {
            $totalAccounts = $this->db->table('user_bank_accounts')
                ->where('user_id', $userId)
                ->countAllResults();

            if ($totalAccounts <= 1) {
                return redirect()->to('/bank-account')->with('error', 'Không thể xóa tài khoản ngân hàng mặc định duy nhất.');
            }
        }

        $this->db->table('user_bank_accounts')
            ->where('id', $id)
            ->delete();

        // If deleted account was default, set another one as default
        if ($account['is_default']) {
            $anotherAccount = $this->db->table('user_bank_accounts')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'ASC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if ($anotherAccount) {
                $this->db->table('user_bank_accounts')
                    ->where('id', $anotherAccount['id'])
                    ->update(['is_default' => 1]);
            }
        }

        return redirect()->to('/bank-account')->with('success', 'Xóa tài khoản ngân hàng thành công.');
    }

    /**
     * Set bank account as default
     */
    public function setDefault($id)
    {
        $userId = session()->get('user_id');

        $account = $this->db->table('user_bank_accounts')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$account) {
            return redirect()->to('/bank-account')->with('error', 'Tài khoản ngân hàng không tồn tại.');
        }

        $this->db->transStart();

        // Unset all defaults for this user
        $this->db->table('user_bank_accounts')
            ->where('user_id', $userId)
            ->update(['is_default' => 0]);

        // Set new default
        $this->db->table('user_bank_accounts')
            ->where('id', $id)
            ->update(['is_default' => 1]);

        $this->db->transComplete();

        return redirect()->to('/bank-account')->with('success', 'Đã đặt tài khoản mặc định.');
    }
}
