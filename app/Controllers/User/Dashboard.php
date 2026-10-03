<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\WalletModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = (int)session()->get('user_id');

        $walletModel = new WalletModel();
        $orderModel  = new OrderModel();

        $wallet = $walletModel->getWalletByUserId($userId);
        $stats  = $orderModel->getUserStats($userId);
        $recentOrders = $orderModel->getUserOrders($userId, null, null, 8, 0);

        return $this->renderView('user/dashboard', [
            'title'        => 'Dashboard | ' . $this->data['site_name'],
            'wallet'       => $wallet,
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
