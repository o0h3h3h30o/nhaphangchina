<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class VnReceivingController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Danh sách bao đang vận chuyển + đã đến VN
     */
    public function index()
    {
        $status = $this->request->getGet('status') ?? '';

        $query = $this->db->table('cn_bags b')
            ->select('b.*, p.username as packed_by_name')
            ->join('users p', 'p.id = b.packed_by', 'left')
            ->whereIn('b.status', ['in_transit', 'arrived_vn', 'unpacked']);

        if ($status) {
            $query->where('b.status', $status);
        }

        $bags = $query->orderBy('b.departed_at', 'DESC')->get()->getResultArray();

        return view('admin/vn_receiving/index', [
            'title'  => 'Kho Viet Nam - Nhan bao',
            'bags'   => $bags,
            'status' => $status,
        ]);
    }

    /**
     * Bao đã đến VN
     */
    public function arrive($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || $bag['status'] !== 'in_transit') {
            return redirect()->back()->with('error', 'Bao khong o trang thai dang chuyen.');
        }

        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->update([
                'status'     => 'arrived_vn',
                'arrived_at' => date('Y-m-d H:i:s'),
            ]);

        // Cập nhật kiện
        $this->db->table('cn_warehouse_parcels')
            ->where('bag_id', $bagId)
            ->update(['status' => 'arrived_vn']);

        return redirect()->back()->with('success', 'Bao ' . $bag['bag_code'] . ' da den kho Viet Nam.');
    }

    /**
     * Dỡ bao - tất cả kiện chuyển received_vn
     */
    public function unpack($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || $bag['status'] !== 'arrived_vn') {
            return redirect()->back()->with('error', 'Bao chua den VN hoac da do.');
        }

        $staffId = session()->get('user_id');
        $now     = date('Y-m-d H:i:s');

        $this->db->transStart();

        // Cập nhật bao
        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->update([
                'status'      => 'unpacked',
                'unpacked_by' => $staffId,
                'unpacked_at' => $now,
            ]);

        // Cập nhật kiện
        $this->db->table('cn_warehouse_parcels')
            ->where('bag_id', $bagId)
            ->update(['status' => 'completed']);

        // Cập nhật đơn ký gửi liên quan → received_vn
        $parcels = $this->db->table('cn_warehouse_parcels')
            ->where('bag_id', $bagId)
            ->where('consignment_order_id IS NOT NULL')
            ->get()->getResultArray();

        $updatedCount = 0;
        foreach ($parcels as $p) {
            $order = $this->db->table('consignment_orders')
                ->where('id', $p['consignment_order_id'])
                ->get()->getRowArray();

            if ($order && in_array($order['status'], ['in_transit_cn_vn', 'received_cn', 'packed_for_truck'])) {
                $this->db->table('consignment_orders')
                    ->where('id', $order['id'])
                    ->update(['status' => 'received_vn', 'updated_at' => $now]);

                $this->db->table('consignment_status_histories')->insert([
                    'consignment_order_id' => $order['id'],
                    'from_status'          => $order['status'],
                    'to_status'            => 'received_vn',
                    'changed_by'           => $staffId,
                    'note'                 => 'Do bao ' . $bag['bag_code'] . ' tai kho VN',
                    'created_at'           => $now,
                ]);

                $this->db->table('tracking_events')->insert([
                    'consignment_order_id' => $order['id'],
                    'event_type'           => 'status_change',
                    'title'                => 'Da nhan tai kho Viet Nam',
                    'description'          => 'Do bao ' . $bag['bag_code'],
                    'location'             => 'Kho Viet Nam',
                    'handler'              => session()->get('user_name'),
                    'created_by'           => $staffId,
                    'event_at'             => $now,
                    'created_at'           => $now,
                ]);

                $updatedCount++;
            }
        }

        $this->db->transComplete();

        return redirect()->back()->with('success',
            'Da do bao ' . $bag['bag_code'] . '. ' . $bag['total_parcels'] . ' kien, ' . $updatedCount . ' don ky gui da cap nhat.');
    }

    /**
     * Danh sách kiện vô danh (chưa gán user)
     */
    public function orphanParcels()
    {
        $search = $this->request->getGet('search') ?? '';

        $query = $this->db->table('cn_warehouse_parcels p')
            ->select('p.*, b.bag_code')
            ->join('cn_bags b', 'b.id = p.bag_id', 'left')
            ->where('p.user_id IS NULL');

        if ($search) {
            $query->like('p.cn_tracking_code', $search);
        }

        $parcels = $query->orderBy('p.received_at', 'DESC')->get()->getResultArray();

        $orphanCount = $this->db->table('cn_warehouse_parcels')
            ->where('user_id IS NULL')
            ->countAllResults();

        return view('admin/vn_receiving/orphan_parcels', [
            'title'       => 'Kien hang vo danh',
            'parcels'     => $parcels,
            'search'      => $search,
            'orphanCount' => $orphanCount,
        ]);
    }

    /**
     * AJAX: Gán user cho kiện vô danh
     */
    public function assignUser()
    {
        $parcelId = (int) $this->request->getPost('parcel_id');
        $userId   = (int) $this->request->getPost('user_id');

        if (!$parcelId || !$userId) {
            return $this->response->setJSON(['error' => 'Thieu thong tin.']);
        }

        $parcel = $this->db->table('cn_warehouse_parcels')->where('id', $parcelId)->get()->getRowArray();
        if (!$parcel) {
            return $this->response->setJSON(['error' => 'Kien hang khong ton tai.']);
        }

        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$user) {
            return $this->response->setJSON(['error' => 'User khong ton tai.']);
        }

        // Cập nhật user_id cho kiện
        $this->db->table('cn_warehouse_parcels')
            ->where('id', $parcelId)
            ->update(['user_id' => $userId]);

        // Auto-match hoặc tạo đơn ký gửi
        $matchedOrder = null;
        if (!empty($parcel['cn_tracking_code'])) {
            $matchedOrder = $this->db->table('consignment_orders')
                ->where('cn_tracking_code', $parcel['cn_tracking_code'])
                ->get()->getRowArray();
        }

        $matchInfo = null;
        if ($matchedOrder) {
            // Đơn đã tồn tại → cập nhật user_id nếu chưa có
            if (empty($matchedOrder['user_id']) || $matchedOrder['user_id'] == 0) {
                $this->db->table('consignment_orders')
                    ->where('id', $matchedOrder['id'])
                    ->update(['user_id' => $userId]);
            }
            $this->db->table('cn_warehouse_parcels')
                ->where('id', $parcelId)
                ->update(['consignment_order_id' => $matchedOrder['id']]);

            $matchInfo = $matchedOrder['order_code'];
        } else {
            // Chưa có đơn → tạo mới
            $orderCode = 'KG' . date('Ymd') . rand(1000, 9999);
            $this->db->table('consignment_orders')->insert([
                'order_code'       => $orderCode,
                'user_id'          => $userId,
                'cn_tracking_code' => $parcel['cn_tracking_code'] ?? '',
                'status'           => 'received_vn',
                'actual_weight'    => $parcel['weight'] ?? 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $newOrderId = $this->db->insertID();

            $this->db->table('cn_warehouse_parcels')
                ->where('id', $parcelId)
                ->update(['consignment_order_id' => $newOrderId]);

            $matchInfo = $orderCode;
        }

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Da gan kien ' . $parcel['cn_tracking_code'] . ' cho ' . $user['username'],
            'username' => $user['username'],
            'matched_order' => $matchInfo,
        ]);
    }

    /**
     * AJAX: Tìm user
     */
    public function searchUsers()
    {
        $q = trim($this->request->getGet('q') ?? '');
        if (strlen($q) < 1) {
            return $this->response->setJSON(['users' => []]);
        }

        $users = $this->db->table('users')
            ->select('id, username, email, phone')
            ->groupStart()
                ->like('username', $q)
                ->orLike('email', $q)
                ->orLike('phone', $q)
            ->groupEnd()
            ->where('status', 'active')
            ->limit(10)
            ->get()->getResultArray();

        return $this->response->setJSON(['users' => $users]);
    }

    /**
     * AJAX: Quét kiện khi dỡ bao (check từng mã)
     */
    public function scanParcel()
    {
        $trackingCode = trim($this->request->getPost('tracking_code') ?? '');
        if (empty($trackingCode)) {
            return $this->response->setJSON(['error' => 'Nhap ma van don.']);
        }

        $parcel = $this->db->table('cn_warehouse_parcels p')
            ->select('p.*, b.bag_code, u.username as user_name')
            ->join('cn_bags b', 'b.id = p.bag_id', 'left')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->where('p.cn_tracking_code', $trackingCode)
            ->get()->getRowArray();

        if (!$parcel) {
            return $this->response->setJSON(['error' => 'Kien hang khong ton tai trong he thong.', 'found' => false]);
        }

        // Kiểm tra đơn ký gửi
        $orderInfo = null;
        if ($parcel['consignment_order_id']) {
            $order = $this->db->table('consignment_orders')
                ->where('id', $parcel['consignment_order_id'])
                ->get()->getRowArray();
            if ($order) {
                $orderInfo = [
                    'order_code' => $order['order_code'],
                    'status'     => $order['status'],
                    'product_name' => $order['product_name'],
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'parcel'  => [
                'id'                => $parcel['id'],
                'cn_tracking_code'  => $parcel['cn_tracking_code'],
                'weight'            => $parcel['weight'],
                'chargeable_weight' => $parcel['chargeable_weight'],
                'bag_code'          => $parcel['bag_code'],
                'status'            => $parcel['status'],
                'user_name'         => $parcel['user_name'],
            ],
            'order' => $orderInfo,
        ]);
    }
}
