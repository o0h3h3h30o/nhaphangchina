<?php

namespace App\Controllers;

class ProfileController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Show and edit user profile
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Get user data
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Get profile
        $profile = $this->db->table('user_profiles')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'full_name' => 'permit_empty|max_length[100]',
                'phone'     => 'permit_empty|min_length[10]|max_length[20]',
                'address'   => 'permit_empty|max_length[500]',
                'city'      => 'permit_empty|max_length[100]',
                'district'  => 'permit_empty|max_length[100]',
                'ward'      => 'permit_empty|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return view('profile/index', [
                    'validation' => $this->validator,
                    'user'       => $user,
                    'profile'    => $profile,
                ]);
            }

            $this->db->transStart();

            // Update user phone
            $userModel->update($userId, [
                'phone' => $this->request->getPost('phone'),
            ]);

            // Update or create profile
            $profileData = [
                'full_name' => $this->request->getPost('full_name'),
                'address'   => $this->request->getPost('address'),
                'city'      => $this->request->getPost('city'),
                'district'  => $this->request->getPost('district'),
                'ward'      => $this->request->getPost('ward'),
            ];

            if ($profile) {
                $this->db->table('user_profiles')
                    ->where('user_id', $userId)
                    ->update($profileData);
            } else {
                $profileData['user_id'] = $userId;
                $this->db->table('user_profiles')->insert($profileData);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->to('/profile')->with('error', 'Đã xảy ra lỗi khi cập nhật hồ sơ.');
            }

            // Update session data
            session()->set([
                'user_phone' => $this->request->getPost('phone'),
            ]);

            return redirect()->to('/profile')->with('success', 'Cập nhật hồ sơ thành công.');
        }

        $data = [
            'title'   => 'Hồ sơ cá nhân',
            'user'    => $user,
            'profile' => $profile,
        ];

        return view('profile/index', $data);
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/profile');
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        $messages = [
            'confirm_password' => [
                'matches' => 'Mật khẩu xác nhận không khớp.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->to('/profile')->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $userId = session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Verify old password
        if (!password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->to('/profile')->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        // Update password
        $userModel->update($userId, [
            'password_hash' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/profile')->with('success', 'Đổi mật khẩu thành công.');
    }
}
