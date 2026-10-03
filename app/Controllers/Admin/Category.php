<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Category extends BaseController
{
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')
                                          ->orderBy('id', 'DESC')
                                          ->findAll();

        return $this->renderView('admin/categories', [
            'title'      => 'Categories | ' . $this->data['site_name'],
            'categories' => $categories,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'       => 'required|min_length[2]|max_length[150]',
            'sort_order' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->insert([
            'name'       => trim($this->request->getPost('name')),
            'icon'       => trim((string)$this->request->getPost('icon')),
            'sort_order' => (int)$this->request->getPost('sort_order'),
            'status'     => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
        ]);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Category created successfully.');
    }

    public function update(int $categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $this->categoryModel->update($categoryId, [
            'name'       => trim($this->request->getPost('name')),
            'icon'       => trim((string)$this->request->getPost('icon')),
            'sort_order' => (int)$this->request->getPost('sort_order'),
            'status'     => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Category updated successfully.');
    }

    public function delete(int $categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $this->categoryModel->delete($categoryId);
        return redirect()->to(site_url('admin/categories'))->with('success', 'Category removed successfully.');
    }
}
