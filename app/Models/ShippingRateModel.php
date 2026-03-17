<?php

namespace App\Models;

use CodeIgniter\Model;

class ShippingRateModel extends Model
{
    protected $table            = 'shipping_rates';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_group_id',
        'route',
        'cargo_type',
        'rate_per_kg',
        'min_weight',
        'rounding_method',
        'extra_fee_fragile',
        'extra_fee_bulky',
        'extra_fee_special',
        'volume_divisor',
        'effective_from',
        'effective_to',
        'is_active',
        'created_by',
    ];
}
