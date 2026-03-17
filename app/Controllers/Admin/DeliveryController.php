<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DeliveryOrderModel;
use App\Models\ConsignmentOrderModel;
use App\Models\UserModel;

class DeliveryController extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = service('session');
    }

    private function checkAdmin()
    {
        $role = $this->session->get('user_role');
        if (!in_array($role, ['admin', 'staff'])) {
            return redirect()->to('/auth/login')->with('error', 'Bạn không có quyền truy cập.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $deliveryModel = new DeliveryOrderModel();
        $builder       = $deliveryModel->builder();

        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('status', $status);
        }

        $search = $this->request->getGet('search');
        if ($search) {
            $builder->groupStart()
                ->like('delivery_code', $search)
                ->orLike('receiver_name', $search)
                ->orLike('receiver_phone', $search)
                ->groupEnd();
        }

        $perPage = 20;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total      = $builder->countAllResults(false);
        $deliveries = $builder->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'title'      => 'Quản lý giao hàng',
            'deliveries' => $deliveries,
            'pager'      => $pager,
            'status'     => $status,
            'search'     => $search,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
        ];

        return view('admin/deliveries/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $orderModel    = new ConsignmentOrderModel();
        $deliveryModel = new DeliveryOrderModel();

        $consignmentOrderId = $this->request->getPost('consignment_order_id');
        $order = $orderModel->find($consignmentOrderId);

        if (!$order) {
            return redirect()->to('/admin/deliveries')->with('error', 'Không tìm thấy đơn ký gửi.');
        }

        // Must be ready_for_delivery and paid
        if ($order['status'] !== 'ready_for_delivery') {
            return redirect()->to('/admin/deliveries')->with('error', 'Đơn hàng phải ở trạng thái sẵn sàng giao hàng.');
        }

        if (!$order['paid']) {
            return redirect()->to('/admin/deliveries')->with('error', 'Đơn hàng chưa được thanh toán.');
        }

        $deliveryCode = 'GH' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $deliveryModel->insert([
            'consignment_order_id' => $order['id'],
            'delivery_code'        => $deliveryCode,
            'receiver_name'        => $order['vn_receiver_name'],
            'receiver_phone'       => $order['vn_receiver_phone'],
            'receiver_address'     => $order['vn_receiver_address'],
            'receiver_city'        => $order['vn_receiver_city'],
            'receiver_district'    => $order['vn_receiver_district'],
            'receiver_ward'        => $order['vn_receiver_ward'],
            'status'               => 'pending',
            'created_by'           => $this->session->get('user_id'),
        ]);

        // Update consignment order status
        $orderModel->update($order['id'], ['status' => 'out_for_delivery']);

        return redirect()->to('/admin/deliveries')->with('success', "Tạo đơn giao hàng {$deliveryCode} thành công.");
    }

    public function assign($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $deliveryModel = new DeliveryOrderModel();
        $userModel     = new UserModel();

        $delivery = $deliveryModel->find($id);
        if (!$delivery) {
            return redirect()->to('/admin/deliveries')->with('error', 'Không tìm thấy đơn giao hàng.');
        }

        $shipperId = $this->request->getPost('shipper_id');
        $shipper   = $userModel->find($shipperId);

        if (!$shipper || !in_array($shipper['role'], ['staff', 'admin'])) {
            return redirect()->to("/admin/deliveries")->with('error', 'Nhân viên giao hàng không hợp lệ.');
        }

        $deliveryModel->update($id, [
            'shipper_id' => $shipperId,
            'status'     => 'assigned',
        ]);

        return redirect()->to("/admin/deliveries")->with('success', 'Đã gán nhân viên giao hàng.');
    }

    public function updateStatus($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $deliveryModel = new DeliveryOrderModel();
        $orderModel    = new ConsignmentOrderModel();

        $delivery = $deliveryModel->find($id);
        if (!$delivery) {
            return redirect()->to('/admin/deliveries')->with('error', 'Không tìm thấy đơn giao hàng.');
        }

        $newStatus = $this->request->getPost('status');

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'delivered') {
            $updateData['delivered_at'] = date('Y-m-d H:i:s');

            // Update consignment order
            if ($delivery['consignment_order_id']) {
                $orderModel->update($delivery['consignment_order_id'], ['status' => 'delivered']);
            }
        } elseif ($newStatus === 'failed') {
            $updateData['failed_reason'] = $this->request->getPost('failed_reason') ?? '';
        } elseif ($newStatus === 'rescheduled') {
            $updateData['scheduled_date'] = $this->request->getPost('scheduled_date');
        }

        $deliveryModel->update($id, $updateData);

        return redirect()->to('/admin/deliveries')->with('success', 'Cập nhật trạng thái giao hàng thành công.');
    }

    public function uploadProof($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $deliveryModel = new DeliveryOrderModel();
        $delivery      = $deliveryModel->find($id);

        if (!$delivery) {
            return redirect()->to('/admin/deliveries')->with('error', 'Không tìm thấy đơn giao hàng.');
        }

        $file = $this->request->getFile('proof_image');

        if (!$file || !$file->isValid()) {
            return redirect()->to("/admin/deliveries")->with('error', 'File không hợp lệ.');
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/delivery_proofs', $newName);

        $deliveryModel->update($id, [
            'proof_image' => 'delivery_proofs/' . $newName,
        ]);

        return redirect()->to('/admin/deliveries')->with('success', 'Tải ảnh xác nhận giao hàng thành công.');
    }
}
