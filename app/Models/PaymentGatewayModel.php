<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentGatewayModel extends Model
{
    protected $table            = 'payment_gateways';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'min_amount',
        'max_amount',
        'fee_percentage',
        'currency_code',
        'credentials',
        'instructions',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveGateways()
    {
        return $this->where('is_active', 1)->findAll();
    }

    public function getDecodedCredentials(array $gateway): array
    {
        if (empty($gateway['credentials'])) {
            return [];
        }
        $decoded = json_decode($gateway['credentials'], true);
        return is_array($decoded) ? $decoded : [];
    }
}
