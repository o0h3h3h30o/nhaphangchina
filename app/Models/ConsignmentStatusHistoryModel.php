<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsignmentStatusHistoryModel extends Model
{
    protected $table            = 'consignment_status_histories';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'consignment_order_id',
        'from_status',
        'to_status',
        'note',
        'changed_by',
        'created_at',
    ];
}
