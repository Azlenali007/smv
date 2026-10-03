<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table            = 'services';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'provider_id',
        'provider_service_id',
        'name',
        'description',
        'min_quantity',
        'max_quantity',
        'rate_per_1k',
        'margin_percentage',
        'provider_rate',
        'provider_currency',
        'status',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveServices(?int $categoryId = null, ?string $search = null)
    {
        $builder = $this->select('services.*, categories.name as category_name')
                        ->join('categories', 'categories.id = services.category_id')
                        ->where('services.status', 'active')
                        ->where('categories.status', 'active');

        if ($categoryId) {
            $builder->where('services.category_id', $categoryId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('services.name', $search)
                    ->orLike('services.description', $search)
                    ->groupEnd();
        }

        return $builder->orderBy('categories.sort_order', 'ASC')
                       ->orderBy('services.sort_order', 'ASC')
                       ->orderBy('services.id', 'ASC')
                       ->findAll();
    }

    public function getServicesWithProviderDetails()
    {
        return $this->select('services.*, categories.name as category_name, providers.name as provider_name')
                    ->join('categories', 'categories.id = services.category_id', 'left')
                    ->join('providers', 'providers.id = services.provider_id', 'left')
                    ->orderBy('services.id', 'DESC')
                    ->findAll();
    }
}
