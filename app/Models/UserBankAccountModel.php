<?php

namespace App\Models;

use CodeIgniter\Model;

class UserBankAccountModel extends Model
{
    protected $table            = 'user_bank_accounts';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'bank_name',
        'account_number',
        'account_holder',
        'branch',
        'is_default',
        'verified',
    ];
}
