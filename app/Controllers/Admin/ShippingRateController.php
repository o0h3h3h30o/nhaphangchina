<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ShippingRateModel;
use App\Models\ShippingRateHistoryModel;

class ShippingRateController extends BaseController
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
        $rates = $db->table('shipping_rates')
            ->select('shipping_rates.*, user_groups.name as group_name')
            ->join('user_groups', 'user_groups.id = shipping_rates.user_group_id', 'left')
            ->orderBy('shipping_rates.effective_from', 'DESC')
            ->get()
            ->getResultArray();

        $userGroups = $db->table('user_groups')->orderBy('id')->get()->getResultArray();

        $data = [
            'title'      => 'Bảng giá vận chuyển',
            'rates'      => $rates,
            'userGroups' => $userGroups,
        ];

        return view('admin/shipping_rates/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {
            $rateModel = new ShippingRateModel();

            $groupId = $this->request->getPost('user_group_id');

            $rateData = [
                'user_group_id'      => $groupId ?: null,
                'route'              => $this->request->getPost('route'),
                'cargo_type'         => $this->request->getPost('cargo_type'),
                'rate_per_kg'        => (float) $this->request->getPost('rate_per_kg'),
                'min_weight'         => (float) $this->request->getPost('min_weight'),
                'rounding_method'    => $this->request->getPost('rounding_method'),
                'extra_fee_fragile'  => (float) $this->request->getPost('extra_fee_fragile'),
                'extra_fee_bulky'    => (float) $this->request->getPost('extra_fee_bulky'),
                'extra_fee_special'  => (float) $this->request->getPost('extra_fee_special'),
                'effective_from'     => $this->request->getPost('effective_from'),
                'effective_to'       => $this->request->getPost('effective_to') ?: null,
                'is_active'          => $this->request->getPost('is_active') ? 1 : 0,
                'created_by'         => $this->session->get('user_id'),
            ];

            $rateModel->insert($rateData);

            return redirect()->to('/admin/shipping-rates')->with('success', 'Tạo bảng giá thành công.');
        }

        $db = \Config\Database::connect();
        $userGroups = $db->table('user_groups')->orderBy('id')->get()->getResultArray();

        $data = [
            'title'      => 'Tạo bảng giá mới',
            'userGroups' => $userGroups,
        ];

        return view('admin/shipping_rates/create', $data);
    }

    public function edit($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $rateModel    = new ShippingRateModel();
        $historyModel = new ShippingRateHistoryModel();

        $rate = $rateModel->find($id);
        if (!$rate) {
            return redirect()->to('/admin/shipping-rates')->with('error', 'Không tìm thấy bảng giá.');
        }

        if ($this->request->getMethod() === 'POST') {
            $fields = [
                'user_group_id', 'route', 'cargo_type', 'rate_per_kg', 'min_weight',
                'rounding_method', 'extra_fee_fragile', 'extra_fee_bulky',
                'extra_fee_special', 'effective_from', 'effective_to', 'is_active',
            ];

            $updateData = [];
            foreach ($fields as $field) {
                $newValue = $this->request->getPost($field);

                if ($field === 'user_group_id') {
                    $newValue = $newValue ?: null;
                }
                if ($field === 'is_active') {
                    $newValue = $newValue ? 1 : 0;
                }
                if ($field === 'effective_to' && empty($newValue)) {
                    $newValue = null;
                }
                if (in_array($field, ['rate_per_kg', 'min_weight', 'extra_fee_fragile', 'extra_fee_bulky', 'extra_fee_special'])) {
                    $newValue = (float) $newValue;
                }

                $oldValue = $rate[$field] ?? null;

                if ((string) $newValue !== (string) $oldValue) {
                    $updateData[$field] = $newValue;

                    // Save history
                    $historyModel->insert([
                        'shipping_rate_id' => $id,
                        'field_changed'    => $field,
                        'old_value'        => (string) $oldValue,
                        'new_value'        => (string) $newValue,
                        'changed_by'       => $this->session->get('user_id'),
                        'created_at'       => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            if (!empty($updateData)) {
                $rateModel->update($id, $updateData);
            }

            return redirect()->to('/admin/shipping-rates')->with('success', 'Cập nhật bảng giá thành công.');
        }

        $db = \Config\Database::connect();
        $userGroups = $db->table('user_groups')->orderBy('id')->get()->getResultArray();

        $data = [
            'title'      => 'Chỉnh sửa bảng giá',
            'rate'       => $rate,
            'userGroups' => $userGroups,
        ];

        return view('admin/shipping_rates/edit', $data);
    }

    public function toggleActive($id)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $rateModel = new ShippingRateModel();
        $rate      = $rateModel->find($id);

        if (!$rate) {
            return redirect()->to('/admin/shipping-rates')->with('error', 'Không tìm thấy bảng giá.');
        }

        $newStatus = $rate['is_active'] ? 0 : 1;
        $rateModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->to('/admin/shipping-rates')->with('success', "Đã {$statusText} bảng giá.");
    }
}
