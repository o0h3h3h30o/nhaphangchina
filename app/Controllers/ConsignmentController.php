<?php

namespace App\Controllers;

class ConsignmentController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * List consignment orders with search/filter/pagination
     */
    public function index()
    {
        $userId  = session()->get('user_id');
        $perPage = 15;

        $builder = $this->db->table('consignment_orders')
            ->where('user_id', $userId);

        // Filter by status
        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('status', $status);
        }

        // Search by order_code, cn_tracking_code, product_name
        $search = $this->request->getGet('search');
        if ($search) {
            $builder->groupStart()
                ->like('order_code', $search)
                ->orLike('cn_tracking_code', $search)
                ->orLike('product_name', $search)
                ->groupEnd();
        }

        // Date range filter
        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to');
        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        // Get total for pagination
        $total = $builder->countAllResults(false);

        // Get current page
        $page   = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        $orders = $builder->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = \Config\Services::pager();

        $data = [
            'title'   => 'Đơn ký gửi',
            'orders'  => $orders,
            'pager'   => $pager->makeLinks($page, $perPage, $total),
            'total'   => $total,
            'status'  => $status,
            'search'  => $search,
            'dateFrom' => $dateFrom,
            'dateTo'  => $dateTo,
        ];

        return view('consignments/index', $data);
    }

    /**
     * Create consignment order
     */
    public function create()
    {
        $userId = session()->get('user_id');

        // Get receiver name from user profile
        $profile = $this->db->table('user_profiles')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();
        $receiverName = $profile['full_name'] ?? session()->get('user_name');

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'cn_tracking_code'    => 'required|max_length[100]',
                'product_description' => 'required',
                'note'                => 'permit_empty',
            ];

            if (!$this->validate($rules)) {
                return view('consignments/create', [
                    'validation'   => $this->validator,
                    'receiverName' => $receiverName,
                ]);
            }

            $orderCode = 'KG' . date('Ymd') . rand(1000, 9999);

            // Ensure unique order code
            while ($this->db->table('consignment_orders')->where('order_code', $orderCode)->countAllResults() > 0) {
                $orderCode = 'KG' . date('Ymd') . rand(1000, 9999);
            }

            $this->db->transStart();

            $this->db->table('consignment_orders')->insert([
                'user_id'              => $userId,
                'order_code'           => $orderCode,
                'cn_tracking_code'     => $this->request->getPost('cn_tracking_code'),
                'product_name'         => $this->request->getPost('product_description'),
                'product_description'  => $this->request->getPost('product_description'),
                'package_count'        => 1,
                'cargo_type'           => $this->request->getPost('cargo_type') ?? 'general',
                'wooden_crating'       => $this->request->getPost('wooden_crating') ? 1 : 0,
                'vn_receiver_name'     => $receiverName,
                'note'                 => $this->request->getPost('note'),
                'status'               => 'submitted',
            ]);

            $orderId = $this->db->insertID();

            // Create status history entry
            $this->db->table('consignment_status_histories')->insert([
                'consignment_order_id' => $orderId,
                'from_status'          => null,
                'to_status'            => 'submitted',
                'note'                 => 'Đơn hàng được tạo.',
                'changed_by'           => $userId,
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Đã xảy ra lỗi khi tạo đơn hàng.');
            }

            return redirect()->to('/consignments/' . $orderId)->with('success', 'Tạo đơn ký gửi thành công. Mã đơn: ' . $orderCode);
        }

        return view('consignments/create', [
            'receiverName' => $receiverName,
        ]);
    }

    /**
     * Show consignment order detail
     */
    public function show($id)
    {
        $userId = session()->get('user_id');

        $order = $this->db->table('consignment_orders')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/consignments')->with('error', 'Đơn hàng không tồn tại.');
        }

        // Get packages
        $packages = $this->db->table('consignment_packages')
            ->where('consignment_order_id', $id)
            ->get()
            ->getResultArray();

        // Get tracking events
        $trackingEvents = $this->db->table('tracking_events')
            ->where('consignment_order_id', $id)
            ->orderBy('event_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get status history
        $statusHistory = $this->db->table('consignment_status_histories')
            ->where('consignment_order_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title'          => 'Chi tiết đơn ' . $order['order_code'],
            'order'          => $order,
            'packages'       => $packages,
            'trackingEvents' => $trackingEvents,
            'statusHistory'  => $statusHistory,
        ];

        return view('consignments/show', $data);
    }

    /**
     * Edit consignment order (only if status is 'draft')
     */
    public function edit($id)
    {
        $userId = session()->get('user_id');

        $order = $this->db->table('consignment_orders')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/consignments')->with('error', 'Đơn hàng không tồn tại.');
        }

        if ($order['status'] !== 'draft') {
            return redirect()->to('/consignments/' . $id)->with('error', 'Chỉ có thể chỉnh sửa đơn ở trạng thái nháp.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'product_name'        => 'required|max_length[255]',
                'cn_tracking_code'    => 'permit_empty|max_length[100]',
                'product_description' => 'permit_empty',
                'package_count'       => 'required|integer|greater_than[0]',
                'estimated_weight'    => 'permit_empty|decimal',
                'declared_value'      => 'permit_empty|decimal',
                'cargo_type'          => 'required|in_list[general,fragile,special]',
                'cn_warehouse'        => 'permit_empty|max_length[100]',
                'vn_receiver_name'    => 'permit_empty|max_length[100]',
                'vn_receiver_phone'   => 'permit_empty|max_length[20]',
                'vn_receiver_address' => 'permit_empty',
                'vn_receiver_city'    => 'permit_empty|max_length[100]',
                'vn_receiver_district' => 'permit_empty|max_length[100]',
                'vn_receiver_ward'    => 'permit_empty|max_length[100]',
                'note'                => 'permit_empty',
            ];

            if (!$this->validate($rules)) {
                return view('consignments/edit', [
                    'validation' => $this->validator,
                    'order'      => $order,
                ]);
            }

            $this->db->table('consignment_orders')
                ->where('id', $id)
                ->update([
                    'cn_tracking_code'     => $this->request->getPost('cn_tracking_code'),
                    'product_name'         => $this->request->getPost('product_name'),
                    'product_description'  => $this->request->getPost('product_description'),
                    'package_count'        => $this->request->getPost('package_count'),
                    'estimated_weight'     => $this->request->getPost('estimated_weight') ?: null,
                    'declared_value'       => $this->request->getPost('declared_value') ?: null,
                    'cargo_type'           => $this->request->getPost('cargo_type'),
                    'cn_warehouse'         => $this->request->getPost('cn_warehouse'),
                    'vn_receiver_name'     => $this->request->getPost('vn_receiver_name'),
                    'vn_receiver_phone'    => $this->request->getPost('vn_receiver_phone'),
                    'vn_receiver_address'  => $this->request->getPost('vn_receiver_address'),
                    'vn_receiver_city'     => $this->request->getPost('vn_receiver_city'),
                    'vn_receiver_district' => $this->request->getPost('vn_receiver_district'),
                    'vn_receiver_ward'     => $this->request->getPost('vn_receiver_ward'),
                    'note'                 => $this->request->getPost('note'),
                ]);

            return redirect()->to('/consignments/' . $id)->with('success', 'Cập nhật đơn hàng thành công.');
        }

        return view('consignments/edit', [
            'title' => 'Chỉnh sửa đơn ' . $order['order_code'],
            'order' => $order,
        ]);
    }

    /**
     * Cancel consignment order (only if draft or submitted)
     */
    public function cancel($id)
    {
        $userId = session()->get('user_id');

        $order = $this->db->table('consignment_orders')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/consignments')->with('error', 'Đơn hàng không tồn tại.');
        }

        if (!in_array($order['status'], ['draft', 'submitted'])) {
            return redirect()->to('/consignments/' . $id)->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại.');
        }

        $this->db->transStart();

        $previousStatus = $order['status'];

        $this->db->table('consignment_orders')
            ->where('id', $id)
            ->update(['status' => 'cancelled']);

        // Create status history entry
        $this->db->table('consignment_status_histories')->insert([
            'consignment_order_id' => $id,
            'from_status'          => $previousStatus,
            'to_status'            => 'cancelled',
            'note'                 => 'Đơn hàng bị hủy bởi khách hàng.',
            'changed_by'           => $userId,
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/consignments/' . $id)->with('error', 'Đã xảy ra lỗi khi hủy đơn hàng.');
        }

        return redirect()->to('/consignments/' . $id)->with('success', 'Đơn hàng đã được hủy.');
    }
}
