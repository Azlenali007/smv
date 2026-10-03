<?php

namespace App\Models;

use CodeIgniter\Model;

class CurrencyModel extends Model
{
    protected $table            = 'currencies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'code',
        'symbol',
        'rate_to_inr',
        'decimal_precision',
        'is_default',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveCurrencies(): array
    {
        return $this->where('is_active', 1)->orderBy('code', 'ASC')->findAll();
    }

    public function findByCode(string $code)
    {
        return $this->where('code', strtoupper($code))->first();
    }

    public function getDefaultCurrency()
    {
        $default = $this->where('is_default', 1)->first();
        if (!$default) {
            $default = $this->findByCode('INR');
        }
        return $default;
    }
}
