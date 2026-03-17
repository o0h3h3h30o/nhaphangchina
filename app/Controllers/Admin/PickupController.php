<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PickupRequestModel;
use App\Models\ConsignmentOrderModel;
use App\Models\ConsignmentStatusHistoryModel;

class PickupController extends BaseController
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

        $db = \Config\Database::connect();
        $builder = $db->table('pickup_requests pr')
            ->select('pr.*, u.username, co.order_code, co.product_name, co.total_fee, co.actual_weight')
            ->join('users u', 'u.id = pr.user_id', 'left')
            ->join('consignment_orders co', 'co.id = pr.consignment_order_id', 'left');

        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('pr.status', $status);
        }

        $perPage = 20;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total   = $builder->countAllResults(false);
        $pickups = $builder->orderBy('pr.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        return view('admin/pickups/index', [
            'title'   => 'Quản lý yêu cầu lấy hàng',
            'pickups' => $pickups,
            'pager'   => $pager,
            'status'  => $status,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ]);
    }

    public function confirm($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $pickupModel = new PickupRequestModel();
        $pickup      = $pickupModel->find($id);

        if (!$pickup) {
            return redirect()->to('/admin/pickups')->with('error', 'Không tìm thấy yêu cầu lấy hàng.');
        }

        if ($pickup['status'] !== 'pending') {
            return redirect()->to('/admin/pickups')->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $pickupModel->update($id, [
            'status'       => 'confirmed',
            'confirmed_by' => $this->session->get('user_id'),
        ]);

        return redirect()->to('/admin/pickups')->with('success', 'Đã xác nhận yêu cầu lấy hàng.');
    }

    public function complete($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $pickupModel  = new PickupRequestModel();
        $orderModel   = new ConsignmentOrderModel();
        $historyModel = new ConsignmentStatusHistoryModel();

        $pickup = $pickupModel->find($id);
        if (!$pickup) {
            return redirect()->to('/admin/pickups')->with('error', 'Không tìm thấy yêu cầu lấy hàng.');
        }

        $pickupModel->update($id, [
            'status'       => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        // Update consignment order to completed
        if (!empty($pickup['consignment_order_id'])) {
            $order = $orderModel->find($pickup['consignment_order_id']);
            if ($order) {
                $oldStatus = $order['status'];
                $orderModel->update($order['id'], ['status' => 'completed']);

                $historyModel->insert([
                    'consignment_order_id' => $order['id'],
                    'from_status'          => $oldStatus,
                    'to_status'            => 'completed',
                    'note'                 => 'Khách đã nhận hàng (lấy tại kho)',
                    'changed_by'           => $this->session->get('user_id'),
                    'created_at'           => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to('/admin/pickups')->with('success', 'Đã hoàn tất lấy hàng.');
    }
}
