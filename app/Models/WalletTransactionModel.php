<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletTransactionModel extends Model
{
    protected $table            = 'wallet_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'opening_balance',
        'closing_balance',
        'currency_code',
        'reference_id',
        'remarks',
        'admin_id',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function getUserTransactions(int $userId, int $limit = 20, int $offset = 0)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('id', 'DESC')
                    ->findAll($limit, $offset);
    }
}
