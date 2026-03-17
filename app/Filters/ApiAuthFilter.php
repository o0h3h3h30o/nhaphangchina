<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Token không hợp lệ']);
        }

        $token = substr($authHeader, 7);
        $db = \Config\Database::connect();

        $row = $db->table('api_tokens')
            ->where('token', $token)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get()->getRowArray();

        if (!$row) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Token hết hạn hoặc không hợp lệ']);
        }

        $user = $db->table('users')
            ->where('id', $row['user_id'])
            ->where('status', 'active')
            ->get()->getRowArray();

        if (!$user) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Tài khoản không tồn tại hoặc bị khóa']);
        }

        $request->apiUserId = $user['id'];
        $request->apiUser = $user;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
