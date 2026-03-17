<?php

namespace App\Models;

use CodeIgniter\Model;

class UserFlagModel extends Model
{
    protected $table            = 'user_flags';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'user_id',
        'flag',
        'note',
        'created_by',
        'created_at',
    ];
}
