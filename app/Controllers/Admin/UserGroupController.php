<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class UserGroupController extends BaseController
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
        $groups = $db->table('user_groups')
            ->select('user_groups.*, COUNT(users.id) as user_count')
            ->join('users', 'users.user_group_id = user_groups.id', 'left')
            ->groupBy('user_groups.id')
            ->orderBy('user_groups.id', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'  => 'Nhóm người dùng',
            'groups' => $groups,
        ];

        return view('admin/user_groups/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {
            $db = \Config\Database::connect();

            $isDefault = $this->request->getPost('is_default') ? 1 : 0;

            // Nếu set is_default, bỏ default của các nhóm khác
            if ($isDefault) {
                $db->table('user_groups')->update(['is_default' => 0]);
            }

            $db->table('user_groups')->insert([
                'name'        => $this->request->getPost('name'),
                'code'        => $this->request->getPost('code'),
                'description' => $this->request->getPost('description'),
                'is_default'  => $isDefault,
            ]);

            return redirect()->to('/admin/user-groups')->with('success', 'Tạo nhóm thành công.');
        }

        $data = [
            'title' => 'Tạo nhóm mới',
        ];

        return view('admin/user_groups/form', $data);
    }

    public function edit($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $group = $db->table('user_groups')->where('id', $id)->get()->getRowArray();

        if (!$group) {
            return redirect()->to('/admin/user-groups')->with('error', 'Không tìm thấy nhóm.');
        }

        if ($this->request->getMethod() === 'POST') {
            $isDefault = $this->request->getPost('is_default') ? 1 : 0;

            if ($isDefault) {
                $db->table('user_groups')->update(['is_default' => 0]);
            }

            $db->table('user_groups')->where('id', $id)->update([
                'name'        => $this->request->getPost('name'),
                'code'        => $this->request->getPost('code'),
                'description' => $this->request->getPost('description'),
                'is_default'  => $isDefault,
            ]);

            return redirect()->to('/admin/user-groups')->with('success', 'Cập nhật nhóm thành công.');
        }

        $data = [
            'title' => 'Sửa nhóm',
            'group' => $group,
        ];

        return view('admin/user_groups/form', $data);
    }

    public function delete($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $group = $db->table('user_groups')->where('id', $id)->get()->getRowArray();

        if (!$group) {
            return redirect()->to('/admin/user-groups')->with('error', 'Không tìm thấy nhóm.');
        }

        if ($group['is_default']) {
            return redirect()->to('/admin/user-groups')->with('error', 'Không thể xóa nhóm mặc định.');
        }

        // Chuyển user về NULL (sẽ dùng nhóm mặc định)
        $db->table('users')->where('user_group_id', $id)->update(['user_group_id' => null]);

        // Xóa shipping rates của nhóm này
        $db->table('shipping_rates')->where('user_group_id', $id)->delete();

        // Xóa nhóm
        $db->table('user_groups')->where('id', $id)->delete();

        return redirect()->to('/admin/user-groups')->with('success', 'Đã xóa nhóm.');
    }
}
