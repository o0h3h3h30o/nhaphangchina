<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class CnWarehouseController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Danh sách kiện hàng kho TQ
     */
    public function index()
    {
        $status = $this->request->getGet('status') ?? '';
        $date   = $this->request->getGet('date') ?? date('Y-m-d');

        $query = $this->db->table('cn_warehouse_parcels p')
            ->select('p.*, u.username as user_name, r.username as received_by_name, b.bag_code')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->join('users r', 'r.id = p.received_by', 'left')
            ->join('cn_bags b', 'b.id = p.bag_id', 'left');

        if ($status) {
            $query->where('p.status', $status);
        }
        if ($date) {
            $query->where('DATE(p.received_at)', $date);
        }

        $parcels = $query->orderBy('p.received_at', 'DESC')->get()->getResultArray();

        // Đếm hôm nay
        $todayCount = $this->db->table('cn_warehouse_parcels')
            ->where('DATE(received_at)', date('Y-m-d'))
            ->countAllResults();

        // Đếm chưa đóng bao
        $unpackedCount = $this->db->table('cn_warehouse_parcels')
            ->where('status', 'received')
            ->where('bag_id IS NULL')
            ->countAllResults();

        return view('admin/cn_warehouse/index', [
            'title'         => 'Kho Trung Quoc',
            'parcels'       => $parcels,
            'todayCount'    => $todayCount,
            'unpackedCount' => $unpackedCount,
            'status'        => $status,
            'date'          => $date,
        ]);
    }

    /**
     * AJAX: Nhập kiện hàng mới vào kho TQ
     */
    public function receive()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setStatusCode(405);
        }

        $trackingCode = trim($this->request->getPost('tracking_code') ?? '');
        $weight       = (float) ($this->request->getPost('weight') ?? 0);
        $weightType   = $this->request->getPost('weight_type') ?? 'actual';
        $length       = (float) ($this->request->getPost('length') ?? 0);
        $width        = (float) ($this->request->getPost('width') ?? 0);
        $height       = (float) ($this->request->getPost('height') ?? 0);
        $note         = trim($this->request->getPost('note') ?? '');
        $userId       = $this->request->getPost('user_id') ? (int) $this->request->getPost('user_id') : null;

        if (empty($trackingCode)) {
            return $this->response->setJSON(['error' => 'Vui long nhap ma van don.']);
        }

        // Nếu theo khối và không nhập cân thực, tự tính từ kích thước
        if ($weightType === 'volume' && $weight <= 0 && $length > 0 && $width > 0 && $height > 0) {
            $weight = round(($length * $width * $height) / 6000, 2);
        }

        if ($weight <= 0) {
            return $this->response->setJSON(['error' => 'Can nang phai lon hon 0.']);
        }

        // Kiểm tra kiện đã nhập chưa
        $existing = $this->db->table('cn_warehouse_parcels')
            ->where('cn_tracking_code', $trackingCode)
            ->get()->getRowArray();

        if ($existing) {
            return $this->response->setJSON([
                'error'            => 'Ma van don nay da duoc nhap kho truoc do.',
                'already_received' => true,
            ]);
        }

        // Tính cân quy đổi
        $volumeWeight    = null;
        $chargeableWeight = $weight;
        $divisor          = 6000;

        if ($weightType === 'volume' && $length > 0 && $width > 0 && $height > 0) {
            // Lấy divisor từ shipping_rates nếu có
            $rate = $this->db->table('shipping_rates')
                ->where('is_active', 1)
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();

            if ($rate && !empty($rate['volume_divisor'])) {
                $divisor = (int) $rate['volume_divisor'];
            }

            $volumeWeight    = round(($length * $width * $height) / $divisor, 2);
            $chargeableWeight = max($weight, $volumeWeight);
        }

        $staffId = session()->get('user_id');

        // Auto-match với đơn ký gửi
        $matchedOrder = $this->db->table('consignment_orders')
            ->where('cn_tracking_code', $trackingCode)
            ->whereIn('status', ['draft', 'submitted'])
            ->get()->getRowArray();

        $consignmentOrderId = null;
        if ($matchedOrder) {
            $consignmentOrderId = $matchedOrder['id'];
            $userId = $userId ?: $matchedOrder['user_id'];
        }

        // Insert kiện hàng
        $parcelData = [
            'cn_tracking_code'    => $trackingCode,
            'consignment_order_id' => $consignmentOrderId,
            'weight'              => $weight,
            'length_cm'           => $length > 0 ? $length : null,
            'width_cm'            => $width > 0 ? $width : null,
            'height_cm'           => $height > 0 ? $height : null,
            'volume_weight'       => $volumeWeight,
            'chargeable_weight'   => $chargeableWeight,
            'volume_divisor'      => $divisor,
            'cargo_type'          => $matchedOrder['cargo_type'] ?? 'general',
            'user_id'             => $userId,
            'received_by'         => $staffId,
            'note'                => $note ?: null,
            'status'              => 'received',
            'received_at'         => date('Y-m-d H:i:s'),
        ];

        $this->db->table('cn_warehouse_parcels')->insert($parcelData);
        $parcelId = $this->db->insertID();

        // Cập nhật hoặc tạo đơn ký gửi
        $matchInfo = null;
        if ($matchedOrder) {
            // Đã có đơn → cập nhật
            $this->db->table('consignment_orders')
                ->where('id', $matchedOrder['id'])
                ->update([
                    'actual_weight'    => $weight,
                    'status'           => 'received_cn',
                    'cn_parcel_id'     => $parcelId,
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);

            $this->db->table('consignment_status_histories')->insert([
                'consignment_order_id' => $matchedOrder['id'],
                'from_status'          => $matchedOrder['status'],
                'to_status'            => 'received_cn',
                'changed_by'           => $staffId,
                'note'                 => 'Nhap kho TQ - auto match',
                'created_at'           => date('Y-m-d H:i:s'),
            ]);

            $this->db->table('tracking_events')->insert([
                'consignment_order_id' => $matchedOrder['id'],
                'event_type'           => 'status_change',
                'title'                => 'Da nhap kho Trung Quoc',
                'description'          => "Can: {$chargeableWeight}kg",
                'location'             => 'Kho Trung Quoc',
                'handler'              => session()->get('user_name'),
                'created_by'           => $staffId,
                'event_at'             => date('Y-m-d H:i:s'),
                'created_at'           => date('Y-m-d H:i:s'),
            ]);

            $matchInfo = [
                'order_code'    => $matchedOrder['order_code'],
                'product_name'  => $matchedOrder['product_name'],
                'user_id'       => $matchedOrder['user_id'],
            ];
        } else {
            // Chưa có đơn → tạo mới (vô danh hoặc có user)
            $orderCode = 'KG' . date('Ymd') . rand(1000, 9999);
            $this->db->table('consignment_orders')->insert([
                'order_code'       => $orderCode,
                'user_id'          => $userId,
                'cn_tracking_code' => $trackingCode,
                'cargo_type'       => 'general',
                'status'           => 'received_cn',
                'actual_weight'    => $weight,
                'cn_parcel_id'     => $parcelId,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $newOrderId = $this->db->insertID();

            // Liên kết kiện với đơn
            $this->db->table('cn_warehouse_parcels')
                ->where('id', $parcelId)
                ->update(['consignment_order_id' => $newOrderId]);

            $matchInfo = [
                'order_code'    => $orderCode,
                'product_name'  => null,
                'user_id'       => $userId,
            ];
        }

        // Lấy thông tin user nếu có
        $userName = null;
        if ($userId) {
            $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $userName = $user['username'] ?? null;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $matchedOrder
                ? 'Nhap kho thanh cong - Da khop don ky gui ' . $matchedOrder['order_code']
                : 'Nhap kho thanh cong - Kien hang moi (chua khop don)',
            'parcel'  => [
                'id'                => $parcelId,
                'cn_tracking_code'  => $trackingCode,
                'weight'            => $weight,
                'volume_weight'     => $volumeWeight,
                'chargeable_weight' => $chargeableWeight,
                'length_cm'         => $length > 0 ? $length : null,
                'width_cm'          => $width > 0 ? $width : null,
                'height_cm'         => $height > 0 ? $height : null,
                'user_name'         => $userName,
                'matched'           => $matchedOrder ? true : false,
                'match_info'        => $matchInfo,
            ],
        ]);
    }

    /**
     * AJAX: Tìm kiện hàng theo mã
     */
    public function search()
    {
        $q = trim($this->request->getGet('q') ?? '');
        if (empty($q)) {
            return $this->response->setJSON(['parcels' => []]);
        }

        $parcels = $this->db->table('cn_warehouse_parcels p')
            ->select('p.*, u.username as user_name')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->like('p.cn_tracking_code', $q)
            ->orderBy('p.received_at', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        return $this->response->setJSON(['parcels' => $parcels]);
    }
}
