<?php

namespace App\Models;

use CodeIgniter\Model;

class ShippingRateHistoryModel extends Model
{
    protected $table            = 'shipping_rate_histories';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'shipping_rate_id',
        'field_changed',
        'old_value',
        'new_value',
        'changed_by',
        'created_at',
    ];
}
