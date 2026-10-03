<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderServiceModel extends Model
{
    protected $table            = 'provider_services';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'provider_id',
        'remote_service_id',
        'name',
        'type',
        'category',
        'rate',
        'min',
        'max',
        'dripfeed',
        'refill',
        'cancel',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getServicesByProvider(int $providerId)
    {
        return $this->where('provider_id', $providerId)
                    ->orderBy('category', 'ASC')
                    ->orderBy('remote_service_id', 'ASC')
                    ->findAll();
    }
}
