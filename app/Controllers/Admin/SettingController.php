<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SettingController extends BaseController
{
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->session = service('session');
        $this->db = \Config\Database::connect();
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

        $results = $this->db->table('site_settings')
            ->orderBy('setting_group', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $groups = [];
        foreach ($results as $row) {
            $groups[$row['setting_group']][] = $row;
        }

        $data = [
            'title'  => 'Cài đặt website',
            'groups' => $groups,
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        // Handle logo upload
        $logo = $this->request->getFile('site_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete old logo
            $oldLogo = $this->db->table('site_settings')
                ->where('setting_key', 'site_logo')
                ->get()->getRowArray();
            if ($oldLogo && !empty($oldLogo['setting_value'])) {
                $oldFile = FCPATH . ltrim($oldLogo['setting_value'], '/');
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            $newName = 'logo_' . time() . '.' . $logo->getClientExtension();
            $logo->move($uploadPath, $newName);

            $this->db->table('site_settings')
                ->where('setting_key', 'site_logo')
                ->update(['setting_value' => '/uploads/' . $newName]);
        }

        // Handle text settings
        $settings = $this->request->getPost('settings');
        if (is_array($settings)) {
            foreach ($settings as $key => $value) {
                $this->db->table('site_settings')
                    ->where('setting_key', $key)
                    ->update(['setting_value' => $value]);
            }
        }

        return redirect()->to('/admin/settings')->with('success', 'Cài đặt đã được cập nhật thành công.');
    }
}
