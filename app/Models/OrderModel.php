<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'service_id',
        'provider_id',
        'provider_service_id',
        'provider_order_id',
        'link',
        'quantity',
        'charge',
        'display_currency',
        'display_charge',
        'start_counter',
        'remains',
        'status',
        'provider_status',
        'api_response',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUserOrders(int $userId, ?string $status = null, ?string $search = null, int $limit = 20, int $offset = 0)
    {
        $builder = $this->select('orders.*, services.name as service_name')
                        ->join('services', 'services.id = orders.service_id', 'left')
                        ->where('orders.user_id', $userId);

        if ($status && in_list($status, ['pending', 'processing', 'completed', 'cancelled', 'refunded'])) {
            $builder->where('orders.status', $status);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('orders.id', $search)
                    ->orLike('orders.link', $search)
                    ->orLike('services.name', $search)
                    ->groupEnd();
        }

        return $builder->orderBy('orders.id', 'DESC')->findAll($limit, $offset);
    }

    public function getUserOrderCount(int $userId, ?string $status = null, ?string $search = null): int
    {
        $builder = $this->where('user_id', $userId);
        if ($status) {
            $builder->where('status', $status);
        }
        if ($search) {
            $builder->groupStart()
                    ->like('id', $search)
                    ->orLike('link', $search)
                    ->groupEnd();
        }
        return $builder->countAllResults();
    }

    public function getUserStats(int $userId): array
    {
        $db = \Config\Database::connect();
        
        $totalOrders = $this->where('user_id', $userId)->countAllResults();
        $pendingOrders = $this->where('user_id', $userId)->whereIn('status', ['pending', 'processing'])->countAllResults();
        $completedOrders = $this->where('user_id', $userId)->where('status', 'completed')->countAllResults();
        $cancelledOrders = $this->where('user_id', $userId)->whereIn('status', ['cancelled', 'refunded'])->countAllResults();
        
        $spendQuery = $db->table('orders')
                         ->selectSum('charge', 'total_spend')
                         ->where('user_id', $userId)
                         ->whereNotIn('status', ['cancelled', 'refunded'])
                         ->get()
                         ->getRow();
        $totalSpend = $spendQuery && $spendQuery->total_spend ? (float)$spendQuery->total_spend : 0.0;

        return [
            'total_orders'     => $totalOrders,
            'pending_orders'   => $pendingOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_spend'      => $totalSpend,
        ];
    }

    public function getAdminStats(): array
    {
        $db = \Config\Database::connect();

        $totalUsers = $db->table('users')->where('role', 'user')->countAllResults();
        $totalOrders = $this->countAllResults();
        $pendingOrders = $this->whereIn('status', ['pending', 'processing'])->countAllResults();
        $completedOrders = $this->where('status', 'completed')->countAllResults();
        $cancelledOrders = $this->whereIn('status', ['cancelled', 'refunded'])->countAllResults();

        $revQuery = $db->table('orders')
                       ->selectSum('charge', 'total_revenue')
                       ->whereNotIn('status', ['cancelled', 'refunded'])
                       ->get()
                       ->getRow();
        $totalRevenue = $revQuery && $revQuery->total_revenue ? (float)$revQuery->total_revenue : 0.0;

        return [
            'total_users'      => $totalUsers,
            'total_orders'     => $totalOrders,
            'pending_orders'   => $pendingOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_revenue'    => $totalRevenue,
        ];
    }
}
