<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class BagController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Danh sách bao
     */
    public function index()
    {
        $status = $this->request->getGet('status') ?? '';

        $query = $this->db->table('cn_bags b')
            ->select('b.*, p.username as packed_by_name, u.username as unpacked_by_name')
            ->join('users p', 'p.id = b.packed_by', 'left')
            ->join('users u', 'u.id = b.unpacked_by', 'left');

        if ($status) {
            $query->where('b.status', $status);
        }

        $bags = $query->orderBy('b.created_at', 'DESC')->get()->getResultArray();

        return view('admin/bags/index', [
            'title'  => 'Quan ly bao hang',
            'bags'   => $bags,
            'status' => $status,
        ]);
    }

    /**
     * Tạo bao mới
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $bagCode = 'BAO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Đảm bảo unique
            while ($this->db->table('cn_bags')->where('bag_code', $bagCode)->countAllResults() > 0) {
                $bagCode = 'BAO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            }

            $this->db->table('cn_bags')->insert([
                'bag_code'  => $bagCode,
                'packed_by' => session()->get('user_id'),
                'note'      => trim($this->request->getPost('note') ?? ''),
                'status'    => 'packing',
            ]);

            $bagId = $this->db->insertID();
            return redirect()->to("/admin/bags/{$bagId}")->with('success', "Da tao bao {$bagCode}");
        }

        return view('admin/bags/create', ['title' => 'Tao bao moi']);
    }

    /**
     * Chi tiết bao + danh sách kiện
     */
    public function show($id)
    {
        $bag = $this->db->table('cn_bags')->where('id', $id)->get()->getRowArray();
        if (!$bag) {
            return redirect()->to('/admin/bags')->with('error', 'Bao khong ton tai.');
        }

        $parcels = $this->db->table('cn_warehouse_parcels p')
            ->select('p.*, u.username as user_name')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->where('p.bag_id', $id)
            ->orderBy('p.updated_at', 'DESC')
            ->get()->getResultArray();

        return view('admin/bags/show', [
            'title'   => 'Bao ' . $bag['bag_code'],
            'bag'     => $bag,
            'parcels' => $parcels,
        ]);
    }

    /**
     * AJAX: Thêm kiện vào bao
     */
    public function addParcel($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag) {
            return $this->response->setJSON(['error' => 'Bao khong ton tai.']);
        }
        if ($bag['status'] !== 'packing') {
            return $this->response->setJSON(['error' => 'Bao da niem phong, khong the them kien.']);
        }

        $trackingCode = trim($this->request->getPost('tracking_code') ?? '');
        if (empty($trackingCode)) {
            return $this->response->setJSON(['error' => 'Nhap ma van don.']);
        }

        $parcel = $this->db->table('cn_warehouse_parcels')
            ->where('cn_tracking_code', $trackingCode)
            ->get()->getRowArray();

        if (!$parcel) {
            return $this->response->setJSON(['error' => 'Kien hang khong ton tai trong kho.']);
        }
        if ($parcel['bag_id']) {
            $existingBag = $this->db->table('cn_bags')->where('id', $parcel['bag_id'])->get()->getRowArray();
            return $this->response->setJSON(['error' => 'Kien da nam trong bao ' . ($existingBag['bag_code'] ?? $parcel['bag_id'])]);
        }
        if ($parcel['status'] !== 'received') {
            return $this->response->setJSON(['error' => 'Kien khong o trang thai cho dong bao.']);
        }

        // Thêm vào bao
        $this->db->table('cn_warehouse_parcels')
            ->where('id', $parcel['id'])
            ->update([
                'bag_id' => $bagId,
                'status' => 'packed',
            ]);

        // Cập nhật tổng bao
        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->set('total_parcels', 'total_parcels + 1', false)
            ->set('total_weight', 'total_weight + ' . (float) $parcel['chargeable_weight'], false)
            ->update();

        // Lấy user name
        $userName = '';
        if ($parcel['user_id']) {
            $u = $this->db->table('users')->where('id', $parcel['user_id'])->get()->getRowArray();
            $userName = $u['username'] ?? '';
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Da them kien ' . $trackingCode . ' vao bao.',
            'parcel'  => [
                'id'                => $parcel['id'],
                'cn_tracking_code'  => $parcel['cn_tracking_code'],
                'weight'            => $parcel['weight'],
                'chargeable_weight' => $parcel['chargeable_weight'],
                'user_name'         => $userName,
                'matched'           => !empty($parcel['consignment_order_id']),
            ],
        ]);
    }

    /**
     * Bỏ kiện khỏi bao
     */
    public function removeParcel($bagId, $parcelId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || $bag['status'] !== 'packing') {
            return redirect()->back()->with('error', 'Khong the bo kien khi bao da niem phong.');
        }

        $parcel = $this->db->table('cn_warehouse_parcels')
            ->where('id', $parcelId)
            ->where('bag_id', $bagId)
            ->get()->getRowArray();

        if (!$parcel) {
            return redirect()->back()->with('error', 'Kien hang khong nam trong bao nay.');
        }

        $this->db->table('cn_warehouse_parcels')
            ->where('id', $parcelId)
            ->update(['bag_id' => null, 'status' => 'received']);

        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->set('total_parcels', 'GREATEST(total_parcels - 1, 0)', false)
            ->set('total_weight', 'GREATEST(total_weight - ' . (float) $parcel['chargeable_weight'] . ', 0)', false)
            ->update();

        return redirect()->back()->with('success', 'Da bo kien ' . $parcel['cn_tracking_code'] . ' khoi bao.');
    }

    /**
     * Niêm phong bao
     */
    public function seal($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || $bag['status'] !== 'packing') {
            return redirect()->back()->with('error', 'Khong the niem phong bao nay.');
        }
        if ($bag['total_parcels'] <= 0) {
            return redirect()->back()->with('error', 'Bao chua co kien hang nao.');
        }

        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->update([
                'status'    => 'sealed',
                'sealed_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->back()->with('success', 'Da niem phong bao ' . $bag['bag_code']);
    }

    /**
     * Xuất kho - bao bắt đầu vận chuyển
     */
    public function depart($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || !in_array($bag['status'], ['sealed'])) {
            return redirect()->back()->with('error', 'Bao chua niem phong hoac da xuat kho.');
        }

        $staffId = session()->get('user_id');
        $now     = date('Y-m-d H:i:s');

        $this->db->transStart();

        // Cập nhật bao
        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->update([
                'status'      => 'in_transit',
                'departed_at' => $now,
            ]);

        // Cập nhật tất cả kiện trong bao
        $this->db->table('cn_warehouse_parcels')
            ->where('bag_id', $bagId)
            ->update(['status' => 'in_transit']);

        // Cập nhật đơn ký gửi liên quan → in_transit_cn_vn
        $parcels = $this->db->table('cn_warehouse_parcels')
            ->where('bag_id', $bagId)
            ->where('consignment_order_id IS NOT NULL')
            ->get()->getResultArray();

        foreach ($parcels as $p) {
            $order = $this->db->table('consignment_orders')
                ->where('id', $p['consignment_order_id'])
                ->get()->getRowArray();

            if ($order && in_array($order['status'], ['received_cn', 'packed_for_truck'])) {
                $this->db->table('consignment_orders')
                    ->where('id', $order['id'])
                    ->update(['status' => 'in_transit_cn_vn', 'updated_at' => $now]);

                $this->db->table('consignment_status_histories')->insert([
                    'consignment_order_id' => $order['id'],
                    'from_status'          => $order['status'],
                    'to_status'            => 'in_transit_cn_vn',
                    'changed_by'           => $staffId,
                    'note'                 => 'Xuat kho - Bao ' . $bag['bag_code'],
                    'created_at'           => $now,
                ]);

                $this->db->table('tracking_events')->insert([
                    'consignment_order_id' => $order['id'],
                    'event_type'           => 'status_change',
                    'title'                => 'Dang van chuyen ve Viet Nam',
                    'description'          => 'Bao ' . $bag['bag_code'],
                    'location'             => 'Kho Trung Quoc',
                    'handler'              => session()->get('user_name'),
                    'created_by'           => $staffId,
                    'event_at'             => $now,
                    'created_at'           => $now,
                ]);
            }
        }

        $this->db->transComplete();

        return redirect()->back()->with('success', 'Da xuat kho bao ' . $bag['bag_code'] . ' voi ' . $bag['total_parcels'] . ' kien.');
    }
}
