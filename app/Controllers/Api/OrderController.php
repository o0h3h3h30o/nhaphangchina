<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;

class OrderController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function create()
    {
        $userId = $this->request->apiUserId;
        $json   = $this->request->getJSON(true) ?: $this->request->getPost();

        $productName = $json['product_name'] ?? '';
        $sourceUrl   = $json['source_url'] ?? '';

        if (empty($productName)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Tên sản phẩm không được để trống']);
        }

        // Get receiver name from profile
        $profile = $this->db->table('user_profiles')
            ->where('user_id', $userId)->get()->getRowArray();
        $user = $this->request->apiUser;
        $receiverName = $profile['full_name'] ?? $user['username'];

        // Generate order code
        $orderCode = 'KG' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $data = [
            'user_id'             => $userId,
            'order_code'          => $orderCode,
            'cn_tracking_code'    => $json['cn_tracking_code'] ?? '',
            'product_name'        => $productName,
            'product_description' => $json['product_description'] ?? $productName,
            'package_count'       => (int) ($json['quantity'] ?? 1),
            'declared_value'      => (float) ($json['product_price'] ?? 0),
            'cargo_type'          => $json['cargo_type'] ?? 'general',
            'vn_receiver_name'    => $receiverName,
            'note'                => $json['note'] ?? '',
            'status'              => 'submitted',
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        // Build note with source URL, image, and SKU selections
        $noteParts = [];
        if (!empty($json['note'])) {
            $noteParts[] = $json['note'];
        }

        // Include SKU selections in note
        if (!empty($json['sku_selections']) && is_array($json['sku_selections'])) {
            $skuParts = [];
            foreach ($json['sku_selections'] as $group => $val) {
                if (is_array($val) && !empty($val['name'])) {
                    $skuParts[] = "{$group}: {$val['name']}";
                }
            }
            if ($skuParts) {
                $noteParts[] = "Tùy chọn: " . implode(', ', $skuParts);
            }
        }

        if (!empty($sourceUrl)) {
            $noteParts[] = "Link: {$sourceUrl}";
        }
        if (!empty($json['product_image'])) {
            $noteParts[] = "Ảnh: {$json['product_image']}";
        }
        $data['note'] = implode("\n", $noteParts);

        // Also include SKU in product_description if present
        if (!empty($json['product_description'])) {
            $data['product_description'] = $json['product_description'];
        }

        $this->db->table('consignment_orders')->insert($data);
        $orderId = $this->db->insertID();

        // Create status history
        $this->db->table('consignment_status_histories')->insert([
            'consignment_order_id' => $orderId,
            'from_status'          => null,
            'to_status'            => 'submitted',
            'changed_by'           => $userId,
            'note'                 => 'Tạo đơn từ extension',
            'created_at'           => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Tạo đơn thành công',
            'order'   => [
                'id'         => $orderId,
                'order_code' => $orderCode,
                'status'     => 'submitted',
            ],
        ]);
    }

    public function list()
    {
        $userId = $this->request->apiUserId;

        $orders = $this->db->table('consignment_orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'orders'  => array_map(function ($o) {
                return [
                    'id'               => $o['id'],
                    'order_code'       => $o['order_code'],
                    'product_name'     => $o['product_name'],
                    'status'           => $o['status'],
                    'declared_value'   => $o['declared_value'],
                    'total_fee'        => $o['total_fee'],
                    'cn_tracking_code' => $o['cn_tracking_code'],
                    'created_at'       => $o['created_at'],
                ];
            }, $orders),
        ]);
    }
}
