<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsignmentPackageModel extends Model
{
    protected $table            = 'consignment_packages';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'consignment_order_id',
        'package_code',
        'description',
        'weight',
        'length',
        'width',
        'height',
        'created_at',
    ];
}
