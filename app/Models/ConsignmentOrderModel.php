<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsignmentOrderModel extends Model
{
    protected $table            = 'consignment_orders';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'order_code',
        'cn_tracking_code',
        'product_name',
        'product_description',
        'package_count',
        'estimated_weight',
        'actual_weight',
        'declared_value',
        'cargo_type',
        'cn_warehouse',
        'vn_receiver_name',
        'vn_receiver_phone',
        'vn_receiver_address',
        'vn_receiver_city',
        'vn_receiver_district',
        'vn_receiver_ward',
        'note',
        'status',
        'shipping_fee',
        'service_fee',
        'extra_fee',
        'total_fee',
        'fee_snapshot',
        'paid',
        'paid_at',
        'truck_trip_id',
    ];
}
