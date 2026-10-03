<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\ServiceModel;
use App\Models\ProviderModel;
use Config\Database;
use Exception;

class OrderService
{
    protected ServiceModel $serviceModel;
    protected OrderModel $orderModel;
    protected WalletService $walletService;
    protected CurrencyService $currencyService;
    protected ProviderApiService $providerApi;

    public function __construct()
    {
        $this->serviceModel    = new ServiceModel();
        $this->orderModel      = new OrderModel();
        $this->walletService   = new WalletService();
        $this->currencyService = new CurrencyService();
        $this->providerApi     = new ProviderApiService();
    }

    /**
     * Calculate price for a service and quantity
     */
    public function calculatePrice(int $serviceId, int $quantity, string $displayCurrency = 'INR'): array
    {
        $service = $this->serviceModel->find($serviceId);
        if (!$service || $service['status'] !== 'active') {
            return ['success' => false, 'error' => 'Service not found or currently inactive.'];
        }

        if ($quantity < (int)$service['min_quantity']) {
            return ['success' => false, 'error' => 'Quantity must be at least ' . number_format($service['min_quantity'])];
        }

        if ($quantity > (int)$service['max_quantity']) {
            return ['success' => false, 'error' => 'Quantity cannot exceed ' . number_format($service['max_quantity'])];
        }

        // rate_per_1k is base INR selling price for 1,000 units
        $ratePer1kInr = (float)$service['rate_per_1k'];
        $chargeInInr = ($ratePer1kInr / 1000.0) * $quantity;
        $chargeInInrFormatted = number_format($chargeInInr, 4, '.', '');

        // Convert to display currency
        $displayCharge = $this->currencyService->convertFromInr($chargeInInr, $displayCurrency);
        $curr = $this->currencyService->getCurrency($displayCurrency);
        $symbol = $curr['symbol'] ?? '₹';

        return [
            'success'              => true,
            'service_id'           => $serviceId,
            'quantity'             => $quantity,
            'charge_in_inr'        => $chargeInInrFormatted,
            'display_charge'       => $displayCharge,
            'display_currency'     => $displayCurrency,
            'symbol'               => $symbol,
            'formatted_display'    => $symbol . $displayCharge,
        ];
    }

    /**
     * Atomically place a new order
     */
    public function placeOrder(int $userId, int $serviceId, string $link, int $quantity, string $displayCurrency = 'INR'): array
    {
        $link = trim($link);
        if (empty($link) || !filter_var($link, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'error' => 'A valid URL link is required.'];
        }

        // Price check & service validation
        $calc = $this->calculatePrice($serviceId, $quantity, $displayCurrency);
        if (!$calc['success']) {
            return $calc;
        }

        $service = $this->serviceModel->find($serviceId);
        $chargeInInr = $calc['charge_in_inr'];
        $displayCharge = $calc['display_charge'];

        $db = Database::connect();
        $db->transStart();

        try {
            // 1. Verify and debit wallet atomically
            $debitRes = $this->walletService->debitForOrder(
                $userId,
                $chargeInInr,
                'PENDING_ORDER',
                "Order for {$service['name']} (Qty: {$quantity})"
            );

            if (!$debitRes['success']) {
                $db->transRollback();
                return $debitRes;
            }

            // 2. Insert order record in MySQL
            $orderData = [
                'user_id'             => $userId,
                'service_id'          => $serviceId,
                'provider_id'         => $service['provider_id'] ?: null,
                'provider_service_id' => $service['provider_service_id'] ?: null,
                'provider_order_id'   => null,
                'link'                => $link,
                'quantity'            => $quantity,
                'charge'              => $chargeInInr,
                'display_currency'    => $displayCurrency,
                'display_charge'      => $displayCharge,
                'start_counter'       => 0,
                'remains'             => $quantity,
                'status'              => 'pending',
                'provider_status'     => 'Pending',
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ];

            $orderId = $this->orderModel->insert($orderData);
            if (!$orderId) {
                $db->transRollback();
                return ['success' => false, 'error' => 'Failed to save order to database.'];
            }

            // Update the wallet transaction reference with the actual order ID
            $db->table('wallet_transactions')
               ->where('user_id', $userId)
               ->where('reference_id', 'PENDING_ORDER')
               ->orderBy('id', 'DESC')
               ->limit(1)
               ->update(['reference_id' => 'ORDER-' . $orderId]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'error' => 'Transaction failed during order creation.'];
            }

            // 3. If connected to a real provider API, dispatch immediately
            if (!empty($service['provider_id']) && !empty($service['provider_service_id'])) {
                $this->dispatchToProvider($orderId);
            }

            return [
                'success'        => true,
                'order_id'       => $orderId,
                'charge_in_inr'  => $chargeInInr,
                'display_charge' => $displayCharge,
                'currency'       => $displayCurrency,
            ];
        } catch (Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => 'Order placement failed: ' . $e->getMessage()];
        }
    }

    /**
     * Dispatch an order to provider API and store actual response
     */
    public function dispatchToProvider(int $orderId): array
    {
        $order = $this->orderModel->find($orderId);
        if (!$order || empty($order['provider_id']) || empty($order['provider_service_id'])) {
            return ['success' => false, 'error' => 'Order is not configured with an automated provider.'];
        }

        $providerModel = new ProviderModel();
        $provider = $providerModel->find($order['provider_id']);
        if (!$provider || $provider['status'] !== 'active') {
            return ['success' => false, 'error' => 'Provider is currently inactive or missing.'];
        }

        $apiResult = $this->providerApi->placeOrder(
            $provider['api_url'],
            $provider['api_key'],
            $order['provider_service_id'],
            $order['link'],
            (int)$order['quantity']
        );

        if ($apiResult['success']) {
            $this->orderModel->update($orderId, [
                'provider_order_id' => $apiResult['provider_order_id'],
                'provider_status'   => 'In Progress',
                'status'            => 'processing',
                'api_response'      => json_encode($apiResult['data'] ?? []),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
            return ['success' => true, 'provider_order_id' => $apiResult['provider_order_id']];
        } else {
            // Keep status pending/failed and record provider error
            $this->orderModel->update($orderId, [
                'provider_status' => 'Provider Error: ' . substr($apiResult['error'], 0, 100),
                'api_response'    => json_encode(['error' => $apiResult['error']]),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            return ['success' => false, 'error' => $apiResult['error']];
        }
    }
}
