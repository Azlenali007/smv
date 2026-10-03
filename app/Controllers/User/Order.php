<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ServiceModel;
use App\Models\OrderModel;
use App\Models\WalletModel;
use App\Services\OrderService;

class Order extends BaseController
{
    protected OrderService $orderService;
    protected OrderModel $orderModel;
    protected ServiceModel $serviceModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->orderService  = new OrderService();
        $this->orderModel    = new OrderModel();
        $this->serviceModel  = new ServiceModel();
        $this->categoryModel = new CategoryModel();
    }

    public function newOrder()
    {
        $userId = (int)session()->get('user_id');
        $walletModel = new WalletModel();
        $wallet = $walletModel->getWalletByUserId($userId);

        $categories = $this->categoryModel->getActiveCategories();
        $services   = $this->serviceModel->getActiveServices();

        $selectedServiceId = $this->request->getGet('service') ? (int)$this->request->getGet('service') : null;

        return $this->renderView('user/order_new', [
            'title'             => 'Place New Order | ' . $this->data['site_name'],
            'wallet'            => $wallet,
            'categories'        => $categories,
            'services'          => $services,
            'selectedServiceId' => $selectedServiceId,
        ]);
    }

    public function calculatePrice()
    {
        $serviceId = (int)$this->request->getPost('service_id');
        $quantity  = (int)$this->request->getPost('quantity');

        $result = $this->orderService->calculatePrice($serviceId, $quantity, $this->currentCurrency);
        return $this->response->setJSON($result);
    }

    public function placeOrder()
    {
        $userId    = (int)session()->get('user_id');
        $serviceId = (int)$this->request->getPost('service_id');
        $link      = trim((string)$this->request->getPost('link'));
        $quantity  = (int)$this->request->getPost('quantity');

        $result = $this->orderService->placeOrder($userId, $serviceId, $link, $quantity, $this->currentCurrency);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }

        return redirect()->to(site_url('orders/' . $result['order_id']))
                         ->with('success', "Order #{$result['order_id']} placed successfully! Charge: {$result['currency']} {$result['display_charge']}");
    }

    public function index()
    {
        $userId = (int)session()->get('user_id');
        $status = $this->request->getGet('status') ? trim($this->request->getGet('status')) : null;
        $search = $this->request->getGet('search') ? trim($this->request->getGet('search')) : null;
        $page   = max(1, (int)$this->request->getGet('page'));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $orders = $this->orderModel->getUserOrders($userId, $status, $search, $limit, $offset);
        $totalOrders = $this->orderModel->getUserOrderCount($userId, $status, $search);
        $totalPages  = ceil($totalOrders / $limit);

        return $this->renderView('user/orders', [
            'title'       => 'My Orders | ' . $this->data['site_name'],
            'orders'      => $orders,
            'status'      => $status,
            'search'      => $search,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalOrders' => $totalOrders,
        ]);
    }

    public function show(int $orderId)
    {
        $userId = (int)session()->get('user_id');
        $order = $this->orderModel->where('id', $orderId)->where('user_id', $userId)->first();

        if (!$order) {
            return redirect()->to(site_url('orders'))->with('error', 'Order not found or unauthorized.');
        }

        $service = $this->serviceModel->find($order['service_id']);

        return $this->renderView('user/order_detail', [
            'title'   => "Order #{$orderId} Details | " . $this->data['site_name'],
            'order'   => $order,
            'service' => $service,
        ]);
    }
}
