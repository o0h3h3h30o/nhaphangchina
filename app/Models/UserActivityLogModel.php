<?php

namespace App\Models;

use CodeIgniter\Model;

class UserActivityLogModel extends Model
{
    protected $table            = 'user_activity_logs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'created_at',
    ];
}
