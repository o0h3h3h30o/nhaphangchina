<?php

namespace App\Controllers;

class DeliveryController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * List delivery orders related to user's consignment orders
     */
    public function index()
    {
        $userId  = session()->get('user_id');
        $perPage = 15;
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $builder = $this->db->table('delivery_orders as do')
            ->select('do.*, co.order_code, co.product_name')
            ->join('consignment_orders co', 'co.id = do.consignment_order_id')
            ->where('co.user_id', $userId);

        $total = $builder->countAllResults(false);

        $deliveries = $builder->orderBy('do.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = \Config\Services::pager();

        $data = [
            'title'      => 'Đơn giao hàng',
            'deliveries' => $deliveries,
            'pager'      => $pager->makeLinks($page, $perPage, $total),
            'total'      => $total,
        ];

        return view('deliveries/index', $data);
    }

    /**
     * Show delivery detail with status history and proofs
     */
    public function show($id)
    {
        $userId = session()->get('user_id');

        $delivery = $this->db->table('delivery_orders as do')
            ->select('do.*, co.order_code, co.product_name, co.user_id as co_user_id')
            ->join('consignment_orders co', 'co.id = do.consignment_order_id')
            ->where('do.id', $id)
            ->get()
            ->getRowArray();

        if (!$delivery || (int) $delivery['co_user_id'] !== (int) $userId) {
            return redirect()->to('/delivery')->with('error', 'Đơn giao hàng không tồn tại.');
        }

        // Get status history
        $statusHistory = $this->db->table('delivery_status_histories')
            ->where('delivery_order_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get delivery proofs
        $proofs = $this->db->table('delivery_proofs')
            ->where('delivery_order_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title'         => 'Chi tiết giao hàng ' . $delivery['delivery_code'],
            'delivery'      => $delivery,
            'statusHistory' => $statusHistory,
            'proofs'        => $proofs,
        ];

        return view('deliveries/show', $data);
    }
}
