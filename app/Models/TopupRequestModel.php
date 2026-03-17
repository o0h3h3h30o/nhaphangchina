<?php

namespace App\Models;

use CodeIgniter\Model;

class TopupRequestModel extends Model
{
    protected $table            = 'topup_requests';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'code',
        'amount',
        'bank_name',
        'transfer_content',
        'receipt_image',
        'status',
        'approved_by',
        'approved_at',
        'reject_reason',
    ];
}
