<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\UserModel;
use App\Models\PaymentTransactionModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $orderModel     = new OrderModel();
        $userModel      = new UserModel();
        $paymentTxModel = new PaymentTransactionModel();

        $stats = $orderModel->getAdminStats();

        // Recent users from real MySQL table
        $recentUsers = $userModel->where('role', 'user')
                                 ->orderBy('id', 'DESC')
                                 ->limit(6)
                                 ->find();

        // Recent orders from real MySQL table
        $recentOrders = $orderModel->select('orders.*, users.name as user_name, services.name as service_name')
                                   ->join('users', 'users.id = orders.user_id')
                                   ->join('services', 'services.id = orders.service_id')
                                   ->orderBy('orders.id', 'DESC')
                                   ->limit(6)
                                   ->find();

        // Recent payment activity from real MySQL table
        $recentPayments = $paymentTxModel->select('payment_transactions.*, users.name as user_name')
                                         ->join('users', 'users.id = payment_transactions.user_id')
                                         ->orderBy('payment_transactions.id', 'DESC')
                                         ->limit(6)
                                         ->find();

        return $this->renderView('admin/dashboard', [
            'title'          => 'Admin Overview | ' . $this->data['site_name'],
            'stats'          => $stats,
            'recentUsers'    => $recentUsers,
            'recentOrders'   => $recentOrders,
            'recentPayments' => $recentPayments,
        ]);
    }
}
