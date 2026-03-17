<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ChinaReceivingController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $role = session()->get('user_role');
        if (!in_array($role, ['admin', 'staff'])) {
            return redirect()->to('/auth/login');
        }

        // Get recent received orders today
        $todayOrders = $this->db->table('consignment_orders')
            ->select('consignment_orders.*, users.username')
            ->join('users', 'users.id = consignment_orders.user_id', 'left')
            ->where('consignment_orders.status', 'received_cn')
            ->where('consignment_orders.updated_at >=', date('Y-m-d 00:00:00'))
            ->orderBy('consignment_orders.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/china_receiving/index', [
            'title'       => 'Nhap hang kho Trung Quoc',
            'todayOrders' => $todayOrders,
            'todayCount'  => count($todayOrders),
        ]);
    }

    /**
     * AJAX: lookup volume_divisor for a cargo_type
     */
    public function getDivisor()
    {
        $cargoType = trim($this->request->getGet('cargo_type') ?? 'general');

        $rate = $this->db->table('shipping_rates')
            ->select('volume_divisor')
            ->where('cargo_type', $cargoType)
            ->where('is_active', 1)
            ->where('effective_from <=', date('Y-m-d'))
            ->groupStart()
                ->where('effective_to IS NULL')
                ->orWhere('effective_to >=', date('Y-m-d'))
            ->groupEnd()
            ->orderBy('effective_from', 'DESC')
            ->get()
            ->getRowArray();

        $divisor = ($rate && !empty($rate['volume_divisor'])) ? (int) $rate['volume_divisor'] : 6000;

        return $this->response->setJSON(['divisor' => $divisor]);
    }

    /**
     * Process: lookup tracking code, save weight, set status to received_cn
     * Supports two modes:
     *   - weight_type=actual: just actual weight (kg)
     *   - weight_type=volume: dimensions (cm) → volumetric weight = L*W*H/6000
     *   Chargeable weight = max(actual_weight, volume_weight)
     */
    public function process()
    {
        $role = session()->get('user_role');
        if (!in_array($role, ['admin', 'staff'])) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $trackingCode = trim($this->request->getPost('tracking_code') ?? '');
        $weightType   = $this->request->getPost('weight_type') ?? 'actual'; // actual | volume
        $weight       = (float) ($this->request->getPost('weight') ?? 0);
        $length       = (float) ($this->request->getPost('length') ?? 0);
        $width        = (float) ($this->request->getPost('width') ?? 0);
        $height       = (float) ($this->request->getPost('height') ?? 0);

        if (!$trackingCode) {
            return $this->response->setJSON(['error' => 'Vui long nhap ma van don.']);
        }
        if ($weight <= 0) {
            return $this->response->setJSON(['error' => 'Can nang thuc phai lon hon 0.']);
        }

        // Validate dimensions for volume type
        if ($weightType === 'volume') {
            if ($length <= 0 || $width <= 0 || $height <= 0) {
                return $this->response->setJSON(['error' => 'Vui long nhap day du kich thuoc (dai, rong, cao).']);
            }
        }

        $order = $this->db->table('consignment_orders')
            ->select('consignment_orders.*, users.username')
            ->join('users', 'users.id = consignment_orders.user_id', 'left')
            ->where('cn_tracking_code', $trackingCode)
            ->get()
            ->getRowArray();

        if (!$order) {
            return $this->response->setJSON(['error' => 'Khong tim thay don voi ma van don: ' . $trackingCode]);
        }

        if ($order['status'] === 'received_cn') {
            return $this->response->setJSON([
                'error' => 'Don hang nay da duoc nhap kho TQ truoc do. Ma don: ' . $order['order_code'],
                'order' => $order,
                'already_received' => true,
            ]);
        }

        if (!in_array($order['status'], ['draft', 'submitted'])) {
            return $this->response->setJSON([
                'error' => 'Don hang khong o trang thai hop le de nhap kho. Trang thai hien tai: ' . $order['status'],
                'order' => $order,
            ]);
        }

        // Lookup volume_divisor from shipping_rates based on order's cargo_type
        $volumeDivisor = 6000; // default fallback
        $cargoType = $order['cargo_type'] ?? 'general';

        $shippingRate = $this->db->table('shipping_rates')
            ->where('cargo_type', $cargoType)
            ->where('is_active', 1)
            ->where('effective_from <=', date('Y-m-d'))
            ->groupStart()
                ->where('effective_to IS NULL')
                ->orWhere('effective_to >=', date('Y-m-d'))
            ->groupEnd()
            ->orderBy('effective_from', 'DESC')
            ->get()
            ->getRowArray();

        if ($shippingRate && !empty($shippingRate['volume_divisor'])) {
            $volumeDivisor = (int) $shippingRate['volume_divisor'];
        }

        // Calculate volumetric weight and chargeable weight
        $volumeWeight    = null;
        $chargeableWeight = $weight;

        if ($weightType === 'volume' && $length > 0 && $width > 0 && $height > 0) {
            $volumeWeight    = round(($length * $width * $height) / $volumeDivisor, 2);
            $chargeableWeight = max($weight, $volumeWeight);
        }

        $previousStatus = $order['status'];
        $userId = session()->get('user_id');

        $this->db->transStart();

        // Update order: weight + dimensions + status
        $updateData = [
            'actual_weight'    => $weight,
            'chargeable_weight' => $chargeableWeight,
            'status'           => 'received_cn',
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        if ($weightType === 'volume') {
            $updateData['package_length']  = $length;
            $updateData['package_width']   = $width;
            $updateData['package_height']  = $height;
            $updateData['volume_weight']   = $volumeWeight;
            $updateData['volume_divisor']  = $volumeDivisor;
        }

        $this->db->table('consignment_orders')
            ->where('id', $order['id'])
            ->update($updateData);

        // Build note
        $note = 'Nhap kho TQ. Can thuc: ' . $weight . 'kg';
        if ($weightType === 'volume') {
            $note .= ' | KT: ' . $length . 'x' . $width . 'x' . $height . 'cm'
                    . ' | He so: /' . $volumeDivisor
                    . ' | Can QD: ' . $volumeWeight . 'kg'
                    . ' | Tinh cuoc: ' . $chargeableWeight . 'kg';
        }

        // Status history
        $this->db->table('consignment_status_histories')->insert([
            'consignment_order_id' => $order['id'],
            'from_status'          => $previousStatus,
            'to_status'            => 'received_cn',
            'note'                 => $note,
            'changed_by'           => $userId,
        ]);

        // Tracking event
        $this->db->table('tracking_events')->insert([
            'consignment_order_id' => $order['id'],
            'event_type'           => 'status_change',
            'title'                => 'Da nhan tai kho Trung Quoc',
            'description'          => 'Hang da duoc nhan tai kho TQ. ' . $note,
            'handler'              => session()->get('user_name') ?? 'staff',
            'created_by'           => $userId,
            'event_at'             => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['error' => 'Loi he thong, vui long thu lai.']);
        }

        $order['actual_weight']    = $weight;
        $order['volume_weight']    = $volumeWeight;
        $order['volume_divisor']   = $volumeDivisor;
        $order['chargeable_weight'] = $chargeableWeight;
        $order['package_length']   = $length ?: null;
        $order['package_width']    = $width ?: null;
        $order['package_height']   = $height ?: null;
        $order['status']           = 'received_cn';

        $message = 'Nhap kho thanh cong! Ma don: ' . $order['order_code'] . ' - Tinh cuoc: ' . $chargeableWeight . 'kg';
        if ($volumeWeight) {
            $message .= ' (QD: ' . $volumeWeight . 'kg, Thuc: ' . $weight . 'kg)';
        }

        return $this->response->setJSON([
            'success'        => true,
            'message'        => $message,
            'order'          => $order,
            'volume_divisor' => $volumeDivisor,
        ]);
    }
}
