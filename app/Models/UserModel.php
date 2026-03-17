<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'username',
        'email',
        'phone',
        'password_hash',
        'role',
        'user_group_id',
        'status',
        'email_verified',
        'phone_verified',
    ];
}
