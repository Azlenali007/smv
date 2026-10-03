<?php

namespace App\Commands;

use App\Models\OrderModel;
use App\Models\ProviderModel;
use App\Services\SmmProviderService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class OrdersDispatch extends BaseCommand
{
    protected $group       = 'SMM Panel';
    protected $name        = 'orders:dispatch';
    protected $description = 'Dispatches queued pending orders to upstream SMM API providers';

    public function run(array $params)
    {
        CLI::write('[APEXPULSE] Starting orders dispatch pipeline...', 'yellow');

        $orderModel = new OrderModel();
        $providerService = new SmmProviderService();

        // Fetch pending orders with linked provider
        $orders = $orderModel->where('status', 'pending')
                             ->where('provider_id IS NOT NULL')
                             ->where('provider_order_id IS NULL')
                             ->findAll(50);

        if (empty($orders)) {
            CLI::write('[APEXPULSE] No pending orders in queue for dispatch.', 'green');
            return;
        }

        $count = 0;
        foreach ($orders as $order) {
            CLI::write("Dispatching Order #{$order['id']} (Service {$order['service_id']}) to provider #{$order['provider_id']}...", 'cyan');
            
            $res = $providerService->placeRemoteOrder($order);
            if ($res['success']) {
                $orderModel->update($order['id'], [
                    'provider_order_id' => $res['order_id'],
                    'status'            => 'processing',
                    'api_response'      => json_encode($res['raw_response'] ?? []),
                ]);
                CLI::write("✓ Order #{$order['id']} successfully accepted by provider! Remote ID: {$res['order_id']}", 'green');
                $count++;
            } else {
                CLI::error("✗ Order #{$order['id']} failed: " . ($res['error'] ?? 'Unknown API response'));
            }
        }

        CLI::write("[APEXPULSE] Dispatch cycle complete. {$count} orders processed.", 'light_green');
    }
}
