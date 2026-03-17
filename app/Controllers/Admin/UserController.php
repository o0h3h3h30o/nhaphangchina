<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Models\WalletModel;
use App\Models\ConsignmentOrderModel;
use App\Models\WalletTransactionModel;

class UserController extends BaseController
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
        $builder = $db->table('users')
            ->select('users.*, user_groups.name as group_name')
            ->join('user_groups', 'user_groups.id = users.user_group_id', 'left');

        // Search by name/email/phone
        $search = $this->request->getGet('search');
        if ($search) {
            $builder->groupStart()
                ->like('users.username', $search)
                ->orLike('users.email', $search)
                ->orLike('users.phone', $search)
                ->groupEnd();
        }

        // Filter by role
        $role = $this->request->getGet('role');
        if ($role) {
            $builder->where('users.role', $role);
        }

        // Filter by status
        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('users.status', $status);
        }

        // Filter by group
        $groupId = $this->request->getGet('group');
        if ($groupId) {
            $builder->where('users.user_group_id', $groupId);
        }

        $perPage = 20;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total = $builder->countAllResults(false);
        $users = $builder->orderBy('users.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        // Lấy danh sách nhóm cho filter
        $userGroups = $db->table('user_groups')->orderBy('id')->get()->getResultArray();

        $data = [
            'title'      => 'Quản lý người dùng',
            'users'      => $users,
            'pager'      => $pager,
            'search'     => $search,
            'role'       => $role,
            'status'     => $status,
            'groupId'    => $groupId,
            'userGroups' => $userGroups,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
        ];

        return view('admin/users/index', $data);
    }

    public function show($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $userModel    = new UserModel();
        $profileModel = new UserProfileModel();
        $walletModel  = new WalletModel();
        $orderModel   = new ConsignmentOrderModel();
        $txModel      = new WalletTransactionModel();

        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Không tìm thấy người dùng.');
        }

        $profile    = $profileModel->where('user_id', $id)->first();
        $wallet     = $walletModel->where('user_id', $id)->first();
        $orderCount = $orderModel->where('user_id', $id)->countAllResults(false);

        $transactionTotal = $txModel
            ->selectSum('amount')
            ->where('user_id', $id)
            ->first();

        // Lấy danh sách nhóm user
        $db = \Config\Database::connect();
        $userGroups = $db->table('user_groups')->orderBy('id')->get()->getResultArray();

        // Lấy tên nhóm hiện tại
        $currentGroup = null;
        if ($user['user_group_id']) {
            $currentGroup = $db->table('user_groups')->where('id', $user['user_group_id'])->get()->getRowArray();
        }

        $data = [
            'title'            => 'Chi tiết người dùng',
            'user'             => $user,
            'profile'          => $profile,
            'wallet'           => $wallet,
            'orderCount'       => $orderCount,
            'transactionTotal' => $transactionTotal['amount'] ?? 0,
            'userGroups'       => $userGroups,
            'currentGroup'     => $currentGroup,
        ];

        return view('admin/users/show', $data);
    }

    public function lock($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $userModel = new UserModel();
        $user      = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Không tìm thấy người dùng.');
        }

        $userModel->update($id, ['status' => 'locked']);

        return redirect()->to("/admin/users/{$id}")->with('success', 'Tài khoản đã bị khóa.');
    }

    public function unlock($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $userModel = new UserModel();
        $user      = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Không tìm thấy người dùng.');
        }

        $userModel->update($id, ['status' => 'active']);

        return redirect()->to("/admin/users/{$id}")->with('success', 'Tài khoản đã được mở khóa.');
    }

    public function flag($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $userModel = new UserModel();
        $user      = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Không tìm thấy người dùng.');
        }

        $flag = $this->request->getPost('flag');
        if (!in_array($flag, ['vip', 'risky', 'debt'])) {
            return redirect()->to("/admin/users/{$id}")->with('error', 'Flag không hợp lệ.');
        }

        $profileModel = new UserProfileModel();
        $profile      = $profileModel->where('user_id', $id)->first();

        if ($profile) {
            $currentNote = $profile['note'] ?? '';
            $flags       = array_filter(explode(',', $currentNote));
            if (!in_array($flag, $flags)) {
                $flags[] = $flag;
            }
            $profileModel->update($profile['id'], ['note' => implode(',', $flags)]);
        } else {
            $profileModel->insert([
                'user_id' => $id,
                'note'    => $flag,
            ]);
        }

        return redirect()->to("/admin/users/{$id}")->with('success', "Đã thêm flag: {$flag}.");
    }

    public function updateGroup($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Không tìm thấy người dùng.');
        }

        $groupId = $this->request->getPost('user_group_id');
        $userModel->update($id, [
            'user_group_id' => $groupId ?: null,
        ]);

        return redirect()->to("/admin/users/{$id}")->with('success', 'Đã cập nhật nhóm user.');
    }
}
