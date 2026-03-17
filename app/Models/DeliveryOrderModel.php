<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryOrderModel extends Model
{
    protected $table            = 'delivery_orders';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'consignment_order_id',
        'delivery_code',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'receiver_city',
        'receiver_district',
        'receiver_ward',
        'shipper_id',
        'status',
        'scheduled_date',
        'delivered_at',
        'failed_reason',
        'proof_image',
        'note',
        'created_by',
    ];
}
