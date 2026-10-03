<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProviderModel;
use App\Models\ProviderServiceModel;
use App\Models\CategoryModel;
use App\Models\ServiceModel;
use App\Services\ProviderApiService;

class Provider extends BaseController
{
    protected ProviderModel $providerModel;
    protected ProviderServiceModel $providerServiceModel;
    protected ProviderApiService $apiService;
    protected CategoryModel $categoryModel;
    protected ServiceModel $serviceModel;

    public function __construct()
    {
        $this->providerModel        = new ProviderModel();
        $this->providerServiceModel = new ProviderServiceModel();
        $this->apiService           = new ProviderApiService();
        $this->categoryModel        = new CategoryModel();
        $this->serviceModel         = new ServiceModel();
    }

    public function index()
    {
        $providers = $this->providerModel->orderBy('id', 'DESC')->findAll();

        return $this->renderView('admin/providers', [
            'title'     => 'API Providers | ' . $this->data['site_name'],
            'providers' => $providers,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'    => 'required|min_length[2]|max_length[150]',
            'api_url' => 'required|valid_url',
            'api_key' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->providerModel->insert([
            'name'     => trim($this->request->getPost('name')),
            'api_url'  => trim($this->request->getPost('api_url')),
            'api_key'  => trim($this->request->getPost('api_key')),
            'currency' => strtoupper(trim((string)$this->request->getPost('currency'))) ?: 'USD',
            'status'   => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
        ]);

        return redirect()->to(site_url('admin/providers'))->with('success', 'Provider added successfully.');
    }

    public function update(int $providerId)
    {
        $provider = $this->providerModel->find($providerId);
        if (!$provider) {
            return redirect()->back()->with('error', 'Provider not found.');
        }

        $data = [
            'name'     => trim($this->request->getPost('name')),
            'api_url'  => trim($this->request->getPost('api_url')),
            'currency' => strtoupper(trim((string)$this->request->getPost('currency'))) ?: 'USD',
            'status'   => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $newKey = trim((string)$this->request->getPost('api_key'));
        if (!empty($newKey)) {
            $data['api_key'] = $newKey;
        }

        $this->providerModel->update($providerId, $data);
        return redirect()->to(site_url('admin/providers'))->with('success', 'Provider settings updated.');
    }

    public function delete(int $providerId)
    {
        $provider = $this->providerModel->find($providerId);
        if (!$provider) {
            return redirect()->back()->with('error', 'Provider not found.');
        }

        $this->providerModel->delete($providerId);
        return redirect()->to(site_url('admin/providers'))->with('success', 'Provider removed.');
    }

    /**
     * Test connection to provider API and fetch actual live balance
     */
    public function testConnection(int $providerId)
    {
        $provider = $this->providerModel->find($providerId);
        if (!$provider) {
            return redirect()->back()->with('error', 'Provider record not found.');
        }

        $result = $this->apiService->getBalance($provider['api_url'], $provider['api_key']);

        if (!$result['success']) {
            return redirect()->back()->with('error', 'Provider Connection Failed: ' . esc($result['error']));
        }

        $this->providerModel->update($providerId, [
            'balance'      => number_format($result['balance'], 4, '.', ''),
            'currency'     => $result['currency'],
            'last_sync_at' => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', "Connection verified! Provider Balance: {$result['currency']} " . number_format($result['balance'], 2));
    }

    /**
     * Step 2 & 3: Send real API request to fetch provider services list
     */
    public function fetchServices(int $providerId)
    {
        $provider = $this->providerModel->find($providerId);
        if (!$provider) {
            return redirect()->to(site_url('admin/providers'))->with('error', 'Provider not found.');
        }

        // Send real API request to Provider API
        $result = $this->apiService->getServices($provider['api_url'], $provider['api_key']);

        if (!$result['success']) {
            return redirect()->to(site_url('admin/providers'))->with('error', 'Failed to retrieve provider services: ' . esc($result['error']));
        }

        // Cache fetched services into provider_services table
        $fetched = $result['services'];
        $db = \Config\Database::connect();
        
        // Remove prior cache for this provider
        $this->providerServiceModel->where('provider_id', $providerId)->delete();

        foreach ($fetched as $item) {
            $remoteId = $item['service'] ?? ($item['id'] ?? null);
            if (!$remoteId) continue;

            $this->providerServiceModel->insert([
                'provider_id'       => $providerId,
                'remote_service_id' => (string)$remoteId,
                'name'              => (string)($item['name'] ?? 'Unnamed Service'),
                'type'              => (string)($item['type'] ?? 'Default'),
                'category'          => (string)($item['category'] ?? 'General'),
                'rate'              => number_format((float)($item['rate'] ?? 0), 4, '.', ''),
                'min'               => (int)($item['min'] ?? 10),
                'max'               => (int)($item['max'] ?? 100000),
                'dripfeed'          => !empty($item['dripfeed']) ? 1 : 0,
                'refill'            => !empty($item['refill']) ? 1 : 0,
                'cancel'            => !empty($item['cancel']) ? 1 : 0,
            ]);
        }

        $cachedServices = $this->providerServiceModel->getServicesByProvider($providerId);
        $categories     = $this->categoryModel->findAll();

        return $this->renderView('admin/provider_import', [
            'title'          => 'Import Services: ' . esc($provider['name']),
            'provider'       => $provider,
            'cachedServices' => $cachedServices,
            'categories'     => $categories,
        ]);
    }

    /**
     * Step 8, 9, 10: Import selected services into MySQL with category mapping & margin
     */
    public function importServices(int $providerId)
    {
        $provider = $this->providerModel->find($providerId);
        if (!$provider) {
            return redirect()->back()->with('error', 'Provider not found.');
        }

        $selected = $this->request->getPost('selected_services');
        $categoryId = (int)$this->request->getPost('target_category_id');
        $marginPercent = (float)$this->request->getPost('margin_percentage');

        if (empty($selected) || !is_array($selected)) {
            return redirect()->back()->with('error', 'Please select at least one service to import.');
        }

        if ($categoryId <= 0) {
            return redirect()->back()->with('error', 'Please select a destination category.');
        }

        $count = 0;
        foreach ($selected as $remoteId) {
            $cached = $this->providerServiceModel->where('provider_id', $providerId)
                                                 ->where('remote_service_id', (string)$remoteId)
                                                 ->first();
            if (!$cached) continue;

            // Pipeline: Provider Rate -> Convert to INR -> Apply Margin
            $calc = $this->currencyService->calculateSellingRate(
                $cached['rate'],
                $provider['currency'] ?: 'USD',
                $marginPercent,
                'INR'
            );

            // Upsert into active services table
            $existing = $this->serviceModel->where('provider_id', $providerId)
                                           ->where('provider_service_id', (string)$remoteId)
                                           ->first();

            $serviceData = [
                'category_id'         => $categoryId,
                'provider_id'         => $providerId,
                'provider_service_id' => (string)$remoteId,
                'name'                => $cached['name'],
                'description'         => "Imported from {$provider['name']} (Service ID: {$remoteId})",
                'min_quantity'        => (int)$cached['min'],
                'max_quantity'        => (int)$cached['max'],
                'provider_rate'       => $cached['rate'],
                'provider_currency'   => $provider['currency'] ?: 'USD',
                'margin_percentage'   => $marginPercent,
                'rate_per_1k'         => $calc['rate_in_inr'],
                'status'              => 'active',
            ];

            if ($existing) {
                $serviceData['updated_at'] = date('Y-m-d H:i:s');
                $this->serviceModel->update($existing['id'], $serviceData);
            } else {
                $this->serviceModel->insert($serviceData);
            }
            $count++;
        }

        return redirect()->to(site_url('admin/services'))->with('success', "Successfully imported {$count} services into catalog with {$marginPercent}% margin.");
    }
}
