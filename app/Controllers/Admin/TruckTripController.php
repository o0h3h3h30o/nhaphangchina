<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TruckTripModel;
use App\Models\ConsignmentOrderModel;
use App\Models\ConsignmentStatusHistoryModel;
use App\Models\TrackingEventModel;

class TruckTripController extends BaseController
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

        $tripModel = new TruckTripModel();
        $builder   = $tripModel->builder();

        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('status', $status);
        }

        $perPage = 20;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total = $builder->countAllResults(false);
        $trips = $builder->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'title'   => 'Quản lý chuyến xe',
            'trips'   => $trips,
            'pager'   => $pager,
            'status'  => $status,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ];

        return view('admin/truck_trips/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {
            $tripModel = new TruckTripModel();

            // Auto generate trip code: XE + date + random
            $tripCode = 'XE' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            $tripData = [
                'trip_code'             => $tripCode,
                'truck_name'            => $this->request->getPost('truck_name'),
                'plate_number'          => $this->request->getPost('plate_number'),
                'route'                 => $this->request->getPost('route'),
                'origin_warehouse'      => $this->request->getPost('origin_warehouse'),
                'destination_warehouse' => $this->request->getPost('destination_warehouse'),
                'loading_date'          => $this->request->getPost('loading_date'),
                'departure_date'        => $this->request->getPost('departure_date'),
                'estimated_arrival'     => $this->request->getPost('estimated_arrival'),
                'status'                => 'draft',
                'note'                  => $this->request->getPost('note'),
                'created_by'            => $this->session->get('user_id'),
            ];

            $tripModel->insert($tripData);

            return redirect()->to('/admin/truck-trips')->with('success', "Tạo chuyến xe {$tripCode} thành công.");
        }

        $data = [
            'title' => 'Tạo chuyến xe mới',
        ];

        return view('admin/truck_trips/create', $data);
    }

    public function show($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $tripModel  = new TruckTripModel();
        $orderModel = new ConsignmentOrderModel();

        $trip = $tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/admin/truck-trips')->with('error', 'Không tìm thấy chuyến xe.');
        }

        $orders = $orderModel->where('truck_trip_id', $id)->findAll();

        $data = [
            'title'  => 'Chi tiết chuyến xe',
            'trip'   => $trip,
            'orders' => $orders,
        ];

        return view('admin/truck_trips/show', $data);
    }

    public function addOrder($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $tripModel    = new TruckTripModel();
        $orderModel   = new ConsignmentOrderModel();
        $historyModel = new ConsignmentStatusHistoryModel();
        $eventModel   = new TrackingEventModel();

        $trip = $tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/admin/truck-trips')->with('error', 'Không tìm thấy chuyến xe.');
        }

        $orderCode = $this->request->getPost('order_code');
        $order     = $orderModel->where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->to("/admin/truck-trips/{$id}")->with('error', 'Không tìm thấy đơn hàng với mã này.');
        }

        // Validate order status must be received_cn
        if ($order['status'] !== 'received_cn') {
            return redirect()->to("/admin/truck-trips/{$id}")->with('error', 'Đơn hàng phải ở trạng thái "Đã nhận tại kho TQ" để thêm vào chuyến xe.');
        }

        $oldStatus = $order['status'];

        $orderModel->update($order['id'], [
            'truck_trip_id' => $id,
            'status'        => 'packed_for_truck',
        ]);

        $historyModel->insert([
            'consignment_order_id' => $order['id'],
            'from_status'          => $oldStatus,
            'to_status'            => 'packed_for_truck',
            'note'                 => "Thêm vào chuyến xe {$trip['trip_code']}",
            'changed_by'           => $this->session->get('user_id'),
            'created_at'           => date('Y-m-d H:i:s'),
        ]);

        $eventModel->insert([
            'consignment_order_id' => $order['id'],
            'event_type'           => 'packed_for_truck',
            'title'                => "Đã xếp lên xe {$trip['trip_code']}",
            'description'          => "Đơn hàng được thêm vào chuyến xe {$trip['trip_code']}",
            'handler'              => $this->session->get('username') ?? 'admin',
            'created_by'           => $this->session->get('user_id'),
            'event_at'             => date('Y-m-d H:i:s'),
            'created_at'           => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/admin/truck-trips/{$id}")->with('success', "Đã thêm đơn {$orderCode} vào chuyến xe.");
    }

    public function removeOrder($id, $orderId)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $tripModel  = new TruckTripModel();
        $orderModel = new ConsignmentOrderModel();

        $trip = $tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/admin/truck-trips')->with('error', 'Không tìm thấy chuyến xe.');
        }

        // Only allow removal if trip is draft or loading
        if (!in_array($trip['status'], ['draft', 'loading'])) {
            return redirect()->to("/admin/truck-trips/{$id}")->with('error', 'Chỉ có thể xóa đơn khi chuyến xe ở trạng thái nháp hoặc đang xếp hàng.');
        }

        $order = $orderModel->find($orderId);
        if (!$order || (int) $order['truck_trip_id'] !== (int) $id) {
            return redirect()->to("/admin/truck-trips/{$id}")->with('error', 'Đơn hàng không thuộc chuyến xe này.');
        }

        $orderModel->update($orderId, [
            'truck_trip_id' => null,
            'status'        => 'received_cn',
        ]);

        $historyModel = new ConsignmentStatusHistoryModel();
        $historyModel->insert([
            'consignment_order_id' => $orderId,
            'from_status'          => 'packed_for_truck',
            'to_status'            => 'received_cn',
            'note'                 => "Xóa khỏi chuyến xe {$trip['trip_code']}",
            'changed_by'           => $this->session->get('user_id'),
            'created_at'           => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/admin/truck-trips/{$id}")->with('success', 'Đã xóa đơn hàng khỏi chuyến xe.');
    }

    public function updateStatus($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $tripModel    = new TruckTripModel();
        $orderModel   = new ConsignmentOrderModel();
        $historyModel = new ConsignmentStatusHistoryModel();
        $eventModel   = new TrackingEventModel();

        $trip = $tripModel->find($id);
        if (!$trip) {
            return redirect()->to('/admin/truck-trips')->with('error', 'Không tìm thấy chuyến xe.');
        }

        $newStatus = $this->request->getPost('status');
        $tripModel->update($id, ['status' => $newStatus]);

        // Update trip date fields based on status
        if ($newStatus === 'departed') {
            $tripModel->update($id, ['departure_date' => date('Y-m-d H:i:s')]);
        } elseif ($newStatus === 'arrived_vn') {
            $tripModel->update($id, ['actual_arrival' => date('Y-m-d H:i:s')]);
        }

        // Update all orders in the trip
        $orders = $orderModel->where('truck_trip_id', $id)->findAll();

        foreach ($orders as $order) {
            $orderStatus = null;
            $eventTitle  = '';

            if ($newStatus === 'departed') {
                $orderStatus = 'in_transit_cn_vn';
                $eventTitle  = 'Đang vận chuyển TQ-VN';
            } elseif ($newStatus === 'arrived_vn') {
                $orderStatus = 'received_vn';
                $eventTitle  = 'Đã đến kho VN';
            }

            if ($orderStatus) {
                $oldOrderStatus = $order['status'];
                $orderModel->update($order['id'], ['status' => $orderStatus]);

                $historyModel->insert([
                    'consignment_order_id' => $order['id'],
                    'from_status'          => $oldOrderStatus,
                    'to_status'            => $orderStatus,
                    'note'                 => "Chuyến xe {$trip['trip_code']} - {$newStatus}",
                    'changed_by'           => $this->session->get('user_id'),
                    'created_at'           => date('Y-m-d H:i:s'),
                ]);

                $eventModel->insert([
                    'consignment_order_id' => $order['id'],
                    'event_type'           => $orderStatus,
                    'title'                => $eventTitle,
                    'description'          => "Chuyến xe {$trip['trip_code']}",
                    'handler'              => $this->session->get('username') ?? 'admin',
                    'created_by'           => $this->session->get('user_id'),
                    'event_at'             => date('Y-m-d H:i:s'),
                    'created_at'           => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to("/admin/truck-trips/{$id}")->with('success', 'Cập nhật trạng thái chuyến xe thành công.');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $tripModel = new TruckTripModel();
        $trip      = $tripModel->find($id);

        if (!$trip) {
            return redirect()->to('/admin/truck-trips')->with('error', 'Không tìm thấy chuyến xe.');
        }

        if ($this->request->getMethod() === 'POST') {
            $updateData = [
                'truck_name'            => $this->request->getPost('truck_name'),
                'plate_number'          => $this->request->getPost('plate_number'),
                'route'                 => $this->request->getPost('route'),
                'origin_warehouse'      => $this->request->getPost('origin_warehouse'),
                'destination_warehouse' => $this->request->getPost('destination_warehouse'),
                'loading_date'          => $this->request->getPost('loading_date'),
                'departure_date'        => $this->request->getPost('departure_date'),
                'estimated_arrival'     => $this->request->getPost('estimated_arrival'),
                'note'                  => $this->request->getPost('note'),
            ];

            $tripModel->update($id, $updateData);

            return redirect()->to("/admin/truck-trips/{$id}")->with('success', 'Cập nhật chuyến xe thành công.');
        }

        $data = [
            'title' => 'Chỉnh sửa chuyến xe',
            'trip'  => $trip,
        ];

        return view('admin/truck_trips/edit', $data);
    }
}
