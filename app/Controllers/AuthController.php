<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Login - GET: show form, POST: authenticate
     */
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', [
                    'validation' => $this->validator,
                ]);
            }

            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('email', $email)->first();

            if (!$user) {
                return view('auth/login', [
                    'error' => 'Email hoặc mật khẩu không đúng.',
                ]);
            }

            if (!password_verify($password, $user['password_hash'])) {
                return view('auth/login', [
                    'error' => 'Email hoặc mật khẩu không đúng.',
                ]);
            }

            if ($user['status'] !== 'active') {
                return view('auth/login', [
                    'error' => 'Tài khoản chưa được kích hoạt hoặc đã bị khóa.',
                ]);
            }

            // Set session data
            session()->set([
                'user_id'    => $user['id'],
                'user_role'  => $user['role'],
                'user_name'  => $user['username'],
                'user_email' => $user['email'],
                'user_phone' => $user['phone'],
                'logged_in'  => true,
            ]);

            return redirect()->to('/dashboard')->with('success', 'Đăng nhập thành công.');
        }

        return view('auth/login');
    }

    /**
     * Register - GET: show form, POST: create user + profile + wallet
     */
    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username'         => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'phone'            => 'permit_empty|min_length[10]|max_length[20]',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
                'full_name'        => 'permit_empty|max_length[100]',
            ];

            $messages = [
                'username' => [
                    'is_unique' => 'Tên đăng nhập đã tồn tại.',
                ],
                'email' => [
                    'is_unique' => 'Email đã được sử dụng.',
                ],
                'password_confirm' => [
                    'matches' => 'Mật khẩu xác nhận không khớp.',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return view('auth/register', [
                    'validation' => $this->validator,
                ]);
            }

            $this->db->transStart();

            // Create user
            $userModel = new \App\Models\UserModel();
            $userId = $userModel->insert([
                'username'      => $this->request->getPost('username'),
                'email'         => $this->request->getPost('email'),
                'phone'         => $this->request->getPost('phone'),
                'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'          => 'user',
                'status'        => 'active',
            ]);

            // Create user profile
            $this->db->table('user_profiles')->insert([
                'user_id'   => $userId,
                'full_name' => $this->request->getPost('full_name') ?: null,
            ]);

            // Create wallet
            $this->db->table('wallets')->insert([
                'user_id' => $userId,
                'balance' => 0.00,
                'locked_balance' => 0.00,
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return view('auth/register', [
                    'error' => 'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.',
                ]);
            }

            return redirect()->to('/auth/login')->with('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
        }

        return view('auth/register');
    }

    /**
     * Logout - destroy session
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Đã đăng xuất.');
    }

    /**
     * Forgot Password - GET: show form, POST: generate token
     */
    public function forgotPassword()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
            ];

            if (!$this->validate($rules)) {
                return view('auth/forgot_password', [
                    'validation' => $this->validator,
                ]);
            }

            $email = $this->request->getPost('email');
            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('email', $email)->first();

            if (!$user) {
                // Don't reveal if email exists or not
                return view('auth/forgot_password', [
                    'success' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu.',
                ]);
            }

            // Generate token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Save to user_verifications
            $this->db->table('user_verifications')->insert([
                'user_id'    => $user['id'],
                'type'       => 'password_reset',
                'token'      => $token,
                'expires_at' => $expiresAt,
                'used'       => 0,
            ]);

            // For now just show message (email sending to be implemented later)
            return view('auth/forgot_password', [
                'success' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu.',
                'debug_token' => ENVIRONMENT === 'development' ? $token : null,
            ]);
        }

        return view('auth/forgot_password');
    }

    /**
     * Reset Password - GET: show form with token, POST: validate token & update password
     */
    public function resetPassword()
    {
        $token = $this->request->getGet('token') ?: $this->request->getPost('token');

        if (!$token) {
            return redirect()->to('/auth/forgot-password')->with('error', 'Token không hợp lệ.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'token'            => 'required',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]',
            ];

            if (!$this->validate($rules)) {
                return view('auth/reset_password', [
                    'validation' => $this->validator,
                    'token'      => $token,
                ]);
            }

            // Find valid token
            $verification = $this->db->table('user_verifications')
                ->where('token', $token)
                ->where('type', 'password_reset')
                ->where('used', 0)
                ->where('expires_at >=', date('Y-m-d H:i:s'))
                ->get()
                ->getRowArray();

            if (!$verification) {
                return view('auth/reset_password', [
                    'error' => 'Token không hợp lệ hoặc đã hết hạn.',
                    'token' => $token,
                ]);
            }

            $this->db->transStart();

            // Update password
            $userModel = new \App\Models\UserModel();
            $userModel->update($verification['user_id'], [
                'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            ]);

            // Mark token as used
            $this->db->table('user_verifications')
                ->where('id', $verification['id'])
                ->update(['used' => 1]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return view('auth/reset_password', [
                    'error' => 'Đã xảy ra lỗi. Vui lòng thử lại.',
                    'token' => $token,
                ]);
            }

            return redirect()->to('/auth/login')->with('success', 'Mật khẩu đã được đặt lại. Vui lòng đăng nhập.');
        }

        // Verify token is valid before showing form
        $verification = $this->db->table('user_verifications')
            ->where('token', $token)
            ->where('type', 'password_reset')
            ->where('used', 0)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$verification) {
            return redirect()->to('/auth/forgot-password')->with('error', 'Token không hợp lệ hoặc đã hết hạn.');
        }

        return view('auth/reset_password', [
            'token' => $token,
        ]);
    }
}
