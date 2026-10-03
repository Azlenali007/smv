<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ServiceModel;

class Service extends BaseController
{
    public function index()
    {
        $categoryModel = new CategoryModel();
        $serviceModel  = new ServiceModel();

        $categoryId = $this->request->getGet('category') ? (int)$this->request->getGet('category') : null;
        $search     = $this->request->getGet('search') ? trim($this->request->getGet('search')) : null;

        $categories = $categoryModel->getActiveCategories();
        $services   = $serviceModel->getActiveServices($categoryId, $search);

        return $this->renderView('user/services', [
            'title'            => 'Services & Pricing | ' . $this->data['site_name'],
            'categories'       => $categories,
            'services'         => $services,
            'selectedCategory' => $categoryId,
            'search'           => $search,
        ]);
    }

    public function byCategory(int $categoryId)
    {
        $serviceModel = new ServiceModel();
        $services = $serviceModel->where('category_id', $categoryId)
                                ->where('status', 'active')
                                ->orderBy('sort_order', 'ASC')
                                ->findAll();

        return $this->response->setJSON($services);
    }
}
