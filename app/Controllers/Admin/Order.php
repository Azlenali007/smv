<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ServiceModel;
use App\Services\OrderService;
use App\Services\WalletService;

class Order extends BaseController
{
    protected OrderModel $orderModel;
    protected ServiceModel $serviceModel;
    protected OrderService $orderService;
    protected WalletService $walletService;

    public function __construct()
    {
        $this->orderModel    = new OrderModel();
        $this->serviceModel  = new ServiceModel();
        $this->orderService  = new OrderService();
        $this->walletService = new WalletService();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ? trim($this->request->getGet('status')) : null;
        $search = $this->request->getGet('search') ? trim($this->request->getGet('search')) : null;

        $builder = $this->orderModel->select('orders.*, users.name as user_name, users.email as user_email, services.name as service_name, providers.name as provider_name')
                                    ->join('users', 'users.id = orders.user_id')
                                    ->join('services', 'services.id = orders.service_id')
                                    ->join('providers', 'providers.id = orders.provider_id', 'left');

        if ($status) {
            $builder->where('orders.status', $status);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('orders.id', $search)
                    ->orLike('orders.link', $search)
                    ->orLike('users.email', $search)
                    ->orLike('orders.provider_order_id', $search)
                    ->groupEnd();
        }

        $orders = $builder->orderBy('orders.id', 'DESC')->findAll(100);

        return $this->renderView('admin/orders', [
            'title'  => 'Order Operations | ' . $this->data['site_name'],
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(int $orderId)
    {
        $order = $this->orderModel->select('orders.*, users.name as user_name, users.email as user_email, services.name as service_name, providers.name as provider_name')
                                  ->join('users', 'users.id = orders.user_id')
                                  ->join('services', 'services.id = orders.service_id')
                                  ->join('providers', 'providers.id = orders.provider_id', 'left')
                                  ->where('orders.id', $orderId)
                                  ->first();

        if (!$order) {
            return redirect()->to(site_url('admin/orders'))->with('error', 'Order not found.');
        }

        return $this->renderView('admin/order_detail', [
            'title' => "Order #{$orderId} Inspection",
            'order' => $order,
        ]);
    }

    public function updateStatus(int $orderId)
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $newStatus = $this->request->getPost('status');
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled', 'refunded'];

        if (!in_array($newStatus, $validStatuses, true)) {
            return redirect()->back()->with('error', 'Invalid status specified.');
        }

        // If status changing to refunded from non-refunded, execute atomic wallet credit
        if (($newStatus === 'refunded' || $newStatus === 'cancelled') && !in_array($order['status'], ['refunded', 'cancelled'], true)) {
            $this->walletService->creditRefund(
                $order['user_id'],
                $order['charge'],
                'REFUND-ORDER-' . $orderId,
                "Admin refund for Order #{$orderId}"
            );
        }

        $this->orderModel->update($orderId, [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', "Order #{$orderId} status set to {$newStatus}.");
    }

    public function resendToProvider(int $orderId)
    {
        $res = $this->orderService->dispatchToProvider($orderId);
        if (!$res['success']) {
            return redirect()->back()->with('error', 'Failed to dispatch: ' . $res['error']);
        }

        return redirect()->back()->with('success', "Order dispatched to provider! Provider Order ID: {$res['provider_order_id']}");
    }
}
