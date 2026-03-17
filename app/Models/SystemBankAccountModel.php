<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemBankAccountModel extends Model
{
    protected $table            = 'system_bank_accounts';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'bank_name',
        'account_number',
        'account_holder',
        'branch',
        'is_active',
    ];
}
