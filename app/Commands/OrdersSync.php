<?php

namespace App\Commands;

use App\Models\OrderModel;
use App\Models\ProviderModel;
use App\Services\SmmProviderService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class OrdersSync extends BaseCommand
{
    protected $group       = 'SMM Panel';
    protected $name        = 'orders:sync';
    protected $description = 'Synchronizes order statuses and remains counters from upstream SMM providers';

    public function run(array $params)
    {
        CLI::write('[APEXPULSE] Synchronizing active order statuses from SMM providers...', 'yellow');

        $orderModel = new OrderModel();
        $providerService = new SmmProviderService();

        // Query orders that are currently processing/pending with a remote provider order ID
        $orders = $orderModel->whereIn('status', ['pending', 'processing'])
                             ->where('provider_order_id IS NOT NULL')
                             ->findAll(100);

        if (empty($orders)) {
            CLI::write('[APEXPULSE] No active provider orders requiring status polling.', 'green');
            return;
        }

        $synced = 0;
        foreach ($orders as $order) {
            $statusRes = $providerService->getOrderStatus($order['provider_id'], $order['provider_order_id']);
            if ($statusRes['success']) {
                $remoteStatus = strtolower($statusRes['status'] ?? '');
                $updateData = [
                    'provider_status' => $remoteStatus,
                ];

                if (isset($statusRes['remains'])) {
                    $updateData['remains'] = (int)$statusRes['remains'];
                }
                if (isset($statusRes['start_count'])) {
                    $updateData['start_counter'] = (int)$statusRes['start_count'];
                }

                // Map standard remote status strings
                if (in_array($remoteStatus, ['completed', 'complete'])) {
                    $updateData['status'] = 'completed';
                } elseif (in_array($remoteStatus, ['canceled', 'cancelled'])) {
                    $updateData['status'] = 'cancelled';
                } elseif (in_array($remoteStatus, ['in progress', 'inprogress', 'processing'])) {
                    $updateData['status'] = 'processing';
                } elseif ($remoteStatus === 'refunded') {
                    $updateData['status'] = 'refunded';
                }

                $orderModel->update($order['id'], $updateData);
                $synced++;
                CLI::write("✓ Order #{$order['id']} synced: [Remote Status: {$remoteStatus}]", 'cyan');
            }
        }

        CLI::write("[APEXPULSE] Orders status synchronization completed. {$synced} orders updated.", 'light_green');
    }
}
