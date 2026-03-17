<?php

namespace App\Models;

use CodeIgniter\Model;

class WithdrawalRequestModel extends Model
{
    protected $table            = 'withdrawal_requests';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'code',
        'bank_account_id',
        'amount',
        'status',
        'approved_by',
        'approved_at',
        'completed_at',
        'reject_reason',
    ];
}
