<?php

namespace App\Models;

use CodeIgniter\Model;

class TrackingEventModel extends Model
{
    protected $table            = 'tracking_events';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'consignment_order_id',
        'event_type',
        'title',
        'description',
        'location',
        'handler',
        'created_by',
        'event_at',
        'created_at',
    ];
}
