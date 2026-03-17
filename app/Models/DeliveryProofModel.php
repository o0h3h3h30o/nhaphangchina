<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryProofModel extends Model
{
    protected $table            = 'delivery_proofs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'delivery_order_id',
        'image_path',
        'note',
        'created_by',
        'created_at',
    ];
}
