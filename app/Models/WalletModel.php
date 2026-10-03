<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletModel extends Model
{
    protected $table            = 'wallets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'balance',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getWalletByUserId(int $userId)
    {
        $wallet = $this->where('user_id', $userId)->first();
        if (!$wallet) {
            $this->insert([
                'user_id' => $userId,
                'balance' => '0.0000',
            ]);
            $wallet = $this->where('user_id', $userId)->first();
        }
        return $wallet;
    }
}
