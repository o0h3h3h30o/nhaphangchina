<?php

namespace App\Models;

use CodeIgniter\Model;

class TruckTripModel extends Model
{
    protected $table            = 'truck_trips';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'trip_code',
        'truck_name',
        'plate_number',
        'route',
        'origin_warehouse',
        'destination_warehouse',
        'loading_date',
        'departure_date',
        'estimated_arrival',
        'actual_arrival',
        'status',
        'note',
        'created_by',
    ];
}
