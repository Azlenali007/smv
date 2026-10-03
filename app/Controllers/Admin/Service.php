<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ServiceModel;
use App\Models\ProviderModel;

class Service extends BaseController
{
    protected ServiceModel $serviceModel;
    protected CategoryModel $categoryModel;
    protected ProviderModel $providerModel;

    public function __construct()
    {
        $this->serviceModel  = new ServiceModel();
        $this->categoryModel = new CategoryModel();
        $this->providerModel = new ProviderModel();
    }

    public function index()
    {
        $services   = $this->serviceModel->getServicesWithProviderDetails();
        $categories = $this->categoryModel->findAll();
        $providers  = $this->providerModel->findAll();

        return $this->renderView('admin/services', [
            'title'      => 'Service Management | ' . $this->data['site_name'],
            'services'   => $services,
            'categories' => $categories,
            'providers'  => $providers,
        ]);
    }

    public function store()
    {
        $rules = [
            'category_id'       => 'required|integer',
            'name'              => 'required|min_length[3]|max_length[255]',
            'min_quantity'      => 'required|integer|greater_than[0]',
            'max_quantity'      => 'required|integer|greater_than[0]',
            'rate_per_1k'       => 'required|numeric|greater_than_equal_to[0]',
            'margin_percentage' => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->serviceModel->insert([
            'category_id'         => (int)$this->request->getPost('category_id'),
            'provider_id'         => $this->request->getPost('provider_id') ? (int)$this->request->getPost('provider_id') : null,
            'provider_service_id' => trim((string)$this->request->getPost('provider_service_id')) ?: null,
            'name'                => trim($this->request->getPost('name')),
            'description'         => trim((string)$this->request->getPost('description')),
            'min_quantity'        => (int)$this->request->getPost('min_quantity'),
            'max_quantity'        => (int)$this->request->getPost('max_quantity'),
            'rate_per_1k'         => number_format((float)$this->request->getPost('rate_per_1k'), 4, '.', ''),
            'margin_percentage'   => (float)($this->request->getPost('margin_percentage') ?? 15.0),
            'provider_rate'       => $this->request->getPost('provider_rate') ? (float)$this->request->getPost('provider_rate') : null,
            'provider_currency'   => $this->request->getPost('provider_currency') ?: 'USD',
            'status'              => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
            'sort_order'          => (int)$this->request->getPost('sort_order'),
        ]);

        return redirect()->to(site_url('admin/services'))->with('success', 'Service added successfully.');
    }

    public function update(int $serviceId)
    {
        $service = $this->serviceModel->find($serviceId);
        if (!$service) {
            return redirect()->back()->with('error', 'Service not found.');
        }

        $this->serviceModel->update($serviceId, [
            'category_id'         => (int)$this->request->getPost('category_id'),
            'provider_id'         => $this->request->getPost('provider_id') ? (int)$this->request->getPost('provider_id') : null,
            'provider_service_id' => trim((string)$this->request->getPost('provider_service_id')) ?: null,
            'name'                => trim($this->request->getPost('name')),
            'description'         => trim((string)$this->request->getPost('description')),
            'min_quantity'        => (int)$this->request->getPost('min_quantity'),
            'max_quantity'        => (int)$this->request->getPost('max_quantity'),
            'rate_per_1k'         => number_format((float)$this->request->getPost('rate_per_1k'), 4, '.', ''),
            'margin_percentage'   => (float)($this->request->getPost('margin_percentage') ?? 15.0),
            'provider_rate'       => $this->request->getPost('provider_rate') ? (float)$this->request->getPost('provider_rate') : null,
            'provider_currency'   => $this->request->getPost('provider_currency') ?: 'USD',
            'status'              => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
            'sort_order'          => (int)$this->request->getPost('sort_order'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/services'))->with('success', 'Service updated successfully.');
    }

    public function toggle(int $serviceId)
    {
        $service = $this->serviceModel->find($serviceId);
        if (!$service) {
            return redirect()->back()->with('error', 'Service not found.');
        }

        $newStatus = ($service['status'] === 'active') ? 'inactive' : 'active';
        $this->serviceModel->update($serviceId, [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', "Service toggled to {$newStatus}.");
    }

    public function delete(int $serviceId)
    {
        $service = $this->serviceModel->find($serviceId);
        if (!$service) {
            return redirect()->back()->with('error', 'Service not found.');
        }

        $this->serviceModel->delete($serviceId);
        return redirect()->to(site_url('admin/services'))->with('success', 'Service deleted successfully.');
    }
}
