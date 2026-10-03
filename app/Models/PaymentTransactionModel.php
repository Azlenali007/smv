<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentTransactionModel extends Model
{
    protected $table            = 'payment_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'gateway_id',
        'transaction_reference',
        'gateway_transaction_id',
        'amount',
        'currency',
        'amount_in_inr',
        'fee',
        'status',
        'gateway_payload',
        'gateway_response',
        'verified_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUserTransactions(int $userId)
    {
        return $this->select('payment_transactions.*, payment_gateways.name as gateway_name')
                    ->join('payment_gateways', 'payment_gateways.id = payment_transactions.gateway_id', 'left')
                    ->where('payment_transactions.user_id', $userId)
                    ->orderBy('payment_transactions.id', 'DESC')
                    ->findAll();
    }

    public function getAllTransactions()
    {
        return $this->select('payment_transactions.*, payment_gateways.name as gateway_name, users.name as user_name, users.email as user_email')
                    ->join('payment_gateways', 'payment_gateways.id = payment_transactions.gateway_id', 'left')
                    ->join('users', 'users.id = payment_transactions.user_id', 'left')
                    ->orderBy('payment_transactions.id', 'DESC')
                    ->findAll();
    }
}
