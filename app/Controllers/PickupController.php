<?php

namespace App\Controllers;

class PickupController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * List pickup requests for current user
     */
    public function index()
    {
        $userId  = session()->get('user_id');
        $perPage = 15;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $total = $this->db->table('pickup_requests')
            ->where('user_id', $userId)
            ->countAllResults();

        $requests = $this->db->table('pickup_requests as pr')
            ->select('pr.*, co.order_code, co.product_name, co.total_fee')
            ->join('consignment_orders co', 'co.id = pr.consignment_order_id')
            ->where('pr.user_id', $userId)
            ->orderBy('pr.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = \Config\Services::pager();

        // Orders at VN warehouse ready for pickup
        $readyOrders = $this->db->table('consignment_orders')
            ->where('user_id', $userId)
            ->whereIn('status', ['received_vn', 'fee_calculated', 'ready_for_pickup'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title'       => 'Yêu cầu lấy hàng',
            'requests'    => $requests,
            'readyOrders' => $readyOrders,
            'pager'       => $pager->makeLinks($page, $perPage, $total),
            'total'       => $total,
        ];

        return view('pickup/index', $data);
    }

    /**
     * Create pickup request for a consignment order that is ready_for_pickup
     */
    public function create()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/pickup');
        }

        $userId             = session()->get('user_id');
        $consignmentOrderId = (int) $this->request->getPost('consignment_order_id');
        $note               = $this->request->getPost('note');
        $receiverName       = trim($this->request->getPost('receiver_name') ?? '');
        $receiverPhone      = trim($this->request->getPost('receiver_phone') ?? '');
        $receiverAddress    = trim($this->request->getPost('receiver_address') ?? '');

        if (empty($receiverName) || empty($receiverPhone) || empty($receiverAddress)) {
            return redirect()->to('/pickup')->with('error', 'Vui lòng nhập đầy đủ thông tin người nhận.');
        }

        // Validate: order belongs to user, at VN warehouse, has fee calculated
        $order = $this->db->table('consignment_orders')
            ->where('id', $consignmentOrderId)
            ->where('user_id', $userId)
            ->whereIn('status', ['received_vn', 'fee_calculated', 'ready_for_pickup'])
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/consignments')->with('error', 'Đơn hàng không hợp lệ hoặc chưa về kho VN.');
        }

        // Check if there's already a pending/active pickup request
        $existingRequest = $this->db->table('pickup_requests')
            ->where('consignment_order_id', $consignmentOrderId)
            ->whereNotIn('status', ['cancelled', 'missed'])
            ->get()
            ->getRowArray();

        if ($existingRequest) {
            return redirect()->to('/consignments/' . $consignmentOrderId)->with('error', 'Đã có yêu cầu lấy hàng cho đơn này.');
        }

        $totalFee = (float) ($order['total_fee'] ?? 0);

        // Auto-calculate fee if not yet calculated
        if ($totalFee <= 0 && (float)($order['actual_weight'] ?? 0) > 0) {
            $calcResult = $this->calculateOrderFee($order);
            if ($calcResult['error'] ?? false) {
                return redirect()->to('/pickup')->with('error', $calcResult['error']);
            }
            $totalFee = $calcResult['total_fee'];

            // Update order with calculated fee
            $this->db->table('consignment_orders')
                ->where('id', $consignmentOrderId)
                ->update([
                    'shipping_fee'  => $calcResult['shipping_fee'],
                    'extra_fee'     => $calcResult['extra_fee'],
                    'total_fee'     => $totalFee,
                    'fee_snapshot'  => json_encode($calcResult['snapshot']),
                ]);
        }

        if ($totalFee <= 0) {
            return redirect()->to('/pickup')->with('error', 'Đơn hàng chưa có cân nặng hoặc chưa tính được phí. Vui lòng liên hệ hỗ trợ.');
        }

        // If fee > 0 and not yet paid, deduct from wallet
        if ($totalFee > 0 && empty($order['paid'])) {
            $wallet = $this->db->query(
                "SELECT * FROM wallets WHERE user_id = ? FOR UPDATE",
                [$userId]
            )->getRowArray();

            if (!$wallet) {
                return redirect()->to('/pickup')->with('error', 'Ví không tồn tại.');
            }

            $balance = (float) $wallet['balance'];
            if ($balance < $totalFee) {
                return redirect()->to('/pickup')->with('error', 'Số dư ví không đủ. Cần ' . number_format($totalFee, 0, ',', '.') . ' VND, hiện có ' . number_format($balance, 0, ',', '.') . ' VND. Vui lòng nạp thêm tiền.');
            }
        }

        $this->db->transStart();

        // Deduct wallet if needed
        if ($totalFee > 0 && empty($order['paid'])) {
            $wallet = $this->db->query(
                "SELECT * FROM wallets WHERE user_id = ? FOR UPDATE",
                [$userId]
            )->getRowArray();

            $balance = (float) $wallet['balance'];
            $newBalance = $balance - $totalFee;

            $this->db->query("UPDATE wallets SET balance = ?, updated_at = ? WHERE id = ?", [
                $newBalance, date('Y-m-d H:i:s'), $wallet['id'],
            ]);

            $this->db->table('wallet_transactions')->insert([
                'wallet_id'      => $wallet['id'],
                'user_id'        => $userId,
                'type'           => 'deduct',
                'amount'         => $totalFee,
                'balance_before' => $balance,
                'balance_after'  => $newBalance,
                'reference_type' => 'consignment_order',
                'reference_id'   => $consignmentOrderId,
                'description'    => 'Thanh toán đơn ký gửi #' . $order['order_code'],
                'created_by'     => $userId,
            ]);

            // Mark order as paid
            $this->db->table('consignment_orders')
                ->where('id', $consignmentOrderId)
                ->update([
                    'paid'    => 1,
                    'paid_at' => date('Y-m-d H:i:s'),
                    'status'  => 'ready_for_pickup',
                ]);

            // Status history for payment
            $this->db->table('consignment_status_histories')->insert([
                'consignment_order_id' => $consignmentOrderId,
                'from_status'          => $order['status'],
                'to_status'            => 'ready_for_pickup',
                'note'                 => 'Thanh toán thành công: ' . number_format($totalFee, 0, ',', '.') . ' VND',
                'changed_by'           => $userId,
            ]);
        }

        // Create pickup request
        $this->db->table('pickup_requests')->insert([
            'user_id'               => $userId,
            'consignment_order_id'  => $consignmentOrderId,
            'status'                => 'requested',
            'note'                  => $note,
            'receiver_name'         => $receiverName,
            'receiver_phone'        => $receiverPhone,
            'receiver_address'      => $receiverAddress,
        ]);

        $pickupId = $this->db->insertID();

        $this->db->table('pickup_status_histories')->insert([
            'pickup_request_id' => $pickupId,
            'from_status'       => null,
            'to_status'         => 'requested',
            'note'              => 'Yêu cầu lấy hàng được tạo.',
            'changed_by'        => $userId,
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi tạo yêu cầu lấy hàng.');
        }

        return redirect()->to('/pickup')->with('success', 'Tạo yêu cầu lấy hàng thành công. Đã trừ ' . number_format($totalFee, 0, ',', '.') . ' VND từ ví.');
    }

    /**
     * Tính phí vận chuyển cho đơn hàng
     */
    private function calculateOrderFee(array $order): array
    {
        $actualWeight = (float) ($order['actual_weight'] ?? 0);
        if ($actualWeight <= 0) {
            return ['error' => 'Đơn hàng chưa có cân nặng.'];
        }

        $userId = $order['user_id'];
        $route = $order['cn_warehouse'] ?: 'CN-VN';
        $cargoType = $order['cargo_type'] ?: 'general';

        // Lấy nhóm user
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $userGroupId = $user['user_group_id'] ?? null;
        if (!$userGroupId) {
            $defaultGroup = $this->db->table('user_groups')->where('is_default', 1)->get()->getRowArray();
            $userGroupId = $defaultGroup['id'] ?? null;
        }

        // Tìm rate với fallback
        $findRate = function($groupCondition) use ($route, $cargoType) {
            $builder = $this->db->table('shipping_rates')
                ->where('is_active', 1)
                ->where('effective_from <=', date('Y-m-d'));

            if ($groupCondition === null) {
                $builder->where('user_group_id IS NULL');
            } else {
                $builder->where('user_group_id', $groupCondition);
            }

            // Chính xác route + cargo_type
            $rate = (clone $builder)->where('route', $route)->where('cargo_type', $cargoType)
                ->orderBy('effective_from', 'DESC')->get()->getRowArray();
            if ($rate) return $rate;

            // Fallback cargo_type = general
            if ($cargoType !== 'general') {
                $rate = (clone $builder)->where('route', $route)->where('cargo_type', 'general')
                    ->orderBy('effective_from', 'DESC')->get()->getRowArray();
                if ($rate) return $rate;
            }

            // Fallback bất kỳ rate nào
            return (clone $builder)->where('route', $route)
                ->orderBy('effective_from', 'DESC')->get()->getRowArray();
        };

        $rate = null;
        if ($userGroupId) {
            $rate = $findRate($userGroupId);
        }
        if (!$rate) {
            $rate = $findRate(null);
        }
        if (!$rate) {
            return ['error' => 'Không tìm thấy bảng giá phù hợp.'];
        }

        // Làm tròn cân nặng
        $roundedWeight = $actualWeight;
        switch ($rate['rounding_method'] ?? '0.5kg') {
            case '0.5kg': $roundedWeight = ceil($actualWeight * 2) / 2; break;
            case '1kg':   $roundedWeight = ceil($actualWeight); break;
        }
        $roundedWeight = max($roundedWeight, (float)($rate['min_weight'] ?? 0));

        // Tính phí
        $shippingFee = $roundedWeight * (float)$rate['rate_per_kg'];
        $extraFee = 0;
        if ($cargoType === 'fragile') $extraFee = (float)($rate['extra_fee_fragile'] ?? 0);
        elseif ($cargoType === 'bulky') $extraFee = (float)($rate['extra_fee_bulky'] ?? 0);
        elseif ($cargoType === 'special') $extraFee = (float)($rate['extra_fee_special'] ?? 0);

        $serviceFee = (float)($order['service_fee'] ?? 0);
        $totalFee = $shippingFee + $extraFee + $serviceFee;

        return [
            'shipping_fee' => $shippingFee,
            'extra_fee'    => $extraFee,
            'total_fee'    => $totalFee,
            'snapshot'     => [
                'rate_id'         => $rate['id'],
                'user_group_id'   => $rate['user_group_id'] ?? null,
                'rate_per_kg'     => $rate['rate_per_kg'],
                'rounding_method' => $rate['rounding_method'] ?? '0.5kg',
                'rounded_weight'  => $roundedWeight,
                'actual_weight'   => $actualWeight,
                'min_weight'      => $rate['min_weight'] ?? 0,
                'shipping_fee'    => $shippingFee,
                'extra_fee'       => $extraFee,
                'service_fee'     => $serviceFee,
                'total_fee'       => $totalFee,
                'calculated_at'   => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Cancel pickup request if status is 'requested'
     */
    public function cancel($id)
    {
        $userId = session()->get('user_id');

        $request = $this->db->table('pickup_requests')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$request) {
            return redirect()->to('/pickup')->with('error', 'Yêu cầu lấy hàng không tồn tại.');
        }

        if ($request['status'] !== 'requested') {
            return redirect()->to('/pickup')->with('error', 'Chỉ có thể hủy yêu cầu ở trạng thái đang chờ.');
        }

        $this->db->transStart();

        $this->db->table('pickup_requests')
            ->where('id', $id)
            ->update(['status' => 'cancelled']);

        $this->db->table('pickup_status_histories')->insert([
            'pickup_request_id' => $id,
            'from_status'       => 'requested',
            'to_status'         => 'cancelled',
            'note'              => 'Yêu cầu lấy hàng bị hủy bởi khách hàng.',
            'changed_by'        => $userId,
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/pickup')->with('error', 'Đã xảy ra lỗi khi hủy yêu cầu.');
        }

        return redirect()->to('/pickup')->with('success', 'Yêu cầu lấy hàng đã được hủy.');
    }
}
