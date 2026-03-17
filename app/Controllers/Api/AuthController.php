<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function login()
    {
        $json = $this->request->getJSON(true) ?: $this->request->getPost();

        $email    = $json['email'] ?? '';
        $password = $json['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Email và mật khẩu không được để trống']);
        }

        $user = $this->db->table('users')
            ->where('email', $email)
            ->get()->getRowArray();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return $this->response->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Email hoặc mật khẩu không đúng']);
        }

        if ($user['status'] !== 'active') {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Tài khoản đã bị khóa']);
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $this->db->table('api_tokens')->insert([
            'user_id'    => $user['id'],
            'token'      => $token,
            'expires_at' => $expiresAt,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role'],
            ],
        ]);
    }

    public function me()
    {
        $user = $this->request->apiUser;

        $profile = $this->db->table('user_profiles')
            ->where('user_id', $user['id'])
            ->get()->getRowArray();

        $wallet = $this->db->table('wallets')
            ->where('user_id', $user['id'])
            ->get()->getRowArray();

        return $this->response->setJSON([
            'success' => true,
            'user'    => [
                'id'        => $user['id'],
                'username'  => $user['username'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'full_name' => $profile['full_name'] ?? $user['username'],
                'phone'     => $profile['phone'] ?? $user['phone'] ?? '',
                'balance'   => $wallet['balance'] ?? '0.00',
            ],
        ]);
    }
}
