<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ServiceModel;

class Home extends BaseController
{
    public function index()
    {
        // If already logged in, redirect to appropriate dashboard
        $session = session();
        if ($session->get('is_logged_in')) {
            if ($session->get('role') === 'admin') {
                return redirect()->to(site_url('admin/dashboard'));
            }
            return redirect()->to(site_url('dashboard'));
        }

        // If not installed yet, redirect to web installer
        if (!file_exists(WRITEPATH . 'installed.lock') && !file_exists(WRITEPATH . 'install.lock')) {
            return redirect()->to(site_url('install'));
        }

        $categories = [];
        $services   = [];

        try {
            $categoryModel = new CategoryModel();
            $serviceModel  = new ServiceModel();

            $categories = $categoryModel->getActiveCategories();
            $services   = $serviceModel->getActiveServices();
        } catch (\Throwable $e) {
            // Safe fallback on database connectivity error
        }

        return $this->renderView('home/index', [
            'title'      => ($this->data['site_name'] ?? 'ApexPulse') . ' | Premium Social Media Marketing Infrastructure',
            'categories' => $categories,
            'services'   => $services,
        ]);
    }

    public function services()
    {
        $categoryModel = new CategoryModel();
        $serviceModel  = new ServiceModel();

        $categoryId = $this->request->getGet('category') ? (int)$this->request->getGet('category') : null;
        $search     = $this->request->getGet('search') ? trim($this->request->getGet('search')) : null;

        $categories = $categoryModel->getActiveCategories();
        $services   = $serviceModel->getActiveServices($categoryId, $search);

        return $this->renderView('home/services', [
            'title'            => 'Services Catalog | ' . $this->data['site_name'],
            'categories'       => $categories,
            'services'         => $services,
            'selectedCategory' => $categoryId,
            'search'           => $search,
        ]);
    }

    public function faq()
    {
        return $this->renderView('home/faq', [
            'title' => 'Frequently Asked Questions | ' . $this->data['site_name'],
        ]);
    }

    public function terms()
    {
        return $this->renderView('home/terms', [
            'title' => 'Terms of Service | ' . $this->data['site_name'],
        ]);
    }
}
