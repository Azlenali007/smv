<?php

namespace App\Services;

class ProviderApiService
{
    /**
     * Send HTTP POST request to provider API
     */
    protected function request(string $apiUrl, array $params): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($apiUrl));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ApexPulse-SMM-Engine/1.0');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($error)) {
            return [
                'success' => false,
                'error'   => 'cURL Connection Error: ' . ($error ?: 'Unable to reach provider API server'),
                'code'    => $httpCode,
            ];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error'   => 'Invalid JSON response from provider: ' . substr(strip_tags($response), 0, 150),
                'raw'     => $response,
                'code'    => $httpCode,
            ];
        }

        if (isset($decoded['error'])) {
            return [
                'success' => false,
                'error'   => is_array($decoded['error']) ? json_encode($decoded['error']) : (string)$decoded['error'],
                'data'    => $decoded,
            ];
        }

        return [
            'success' => true,
            'data'    => $decoded,
            'code'    => $httpCode,
        ];
    }

    /**
     * Check Provider Account Balance
     */
    public function getBalance(string $apiUrl, string $apiKey): array
    {
        $result = $this->request($apiUrl, [
            'key'    => $apiKey,
            'action' => 'balance',
        ]);

        if (!$result['success']) {
            return $result;
        }

        $data = $result['data'];
        $balance = $data['balance'] ?? ($data['funds'] ?? null);
        $currency = $data['currency'] ?? 'USD';

        if ($balance === null) {
            return [
                'success' => false,
                'error'   => 'Provider did not return a balance field.',
            ];
        }

        return [
            'success'  => true,
            'balance'  => (float)$balance,
            'currency' => strtoupper((string)$currency),
        ];
    }

    /**
     * Fetch Provider Services list
     */
    public function getServices(string $apiUrl, string $apiKey): array
    {
        $result = $this->request($apiUrl, [
            'key'    => $apiKey,
            'action' => 'services',
        ]);

        if (!$result['success']) {
            return $result;
        }

        $data = $result['data'];
        if (!is_array($data)) {
            return [
                'success' => false,
                'error'   => 'Services list received is not a valid list.',
            ];
        }

        return [
            'success'  => true,
            'services' => $data,
        ];
    }

    /**
     * Send New Order to Provider
     */
    public function placeOrder(string $apiUrl, string $apiKey, string $remoteServiceId, string $link, int $quantity, ?array $extra = []): array
    {
        $params = array_merge([
            'key'     => $apiKey,
            'action'  => 'add',
            'service' => $remoteServiceId,
            'link'    => $link,
            'quantity'=> $quantity,
        ], $extra ?? []);

        $result = $this->request($apiUrl, $params);

        if (!$result['success']) {
            return $result;
        }

        $data = $result['data'];
        $providerOrderId = $data['order'] ?? ($data['order_id'] ?? null);

        if (!$providerOrderId) {
            return [
                'success' => false,
                'error'   => 'Provider succeeded but did not return an order ID: ' . json_encode($data),
                'data'    => $data,
            ];
        }

        return [
            'success'           => true,
            'provider_order_id' => (string)$providerOrderId,
            'data'              => $data,
        ];
    }

    /**
     * Check Order Status from Provider
     */
    public function getOrderStatus(string $apiUrl, string $apiKey, string $providerOrderId): array
    {
        $result = $this->request($apiUrl, [
            'key'    => $apiKey,
            'action' => 'status',
            'order'  => $providerOrderId,
        ]);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'data'    => $result['data'],
        ];
    }
}
