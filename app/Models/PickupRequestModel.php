<?php

namespace App\Models;

use CodeIgniter\Model;

class PickupRequestModel extends Model
{
    protected $table            = 'pickup_requests';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'consignment_order_id',
        'user_id',
        'pickup_address',
        'pickup_city',
        'pickup_district',
        'pickup_ward',
        'contact_name',
        'contact_phone',
        'preferred_date',
        'preferred_time',
        'scheduled_date',
        'scheduled_time',
        'status',
        'note',
        'confirmed_by',
        'completed_at',
    ];
}
