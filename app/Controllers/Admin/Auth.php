<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('is_logged_in') && session()->get('role') === 'admin') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return $this->renderView('admin/login', [
            'title' => 'Admin Security Gateway | ' . $this->data['site_name'],
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $admin = $userModel->where('email', $email)->where('role', 'admin')->first();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Administrator credentials invalid.');
        }

        if ($admin['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Administrator account is deactivated.');
        }

        session()->set([
            'is_logged_in' => true,
            'user_id'      => (int)$admin['id'],
            'user_name'    => $admin['name'],
            'user_email'   => $admin['email'],
            'role'         => 'admin',
        ]);

        return redirect()->to(site_url('admin/dashboard'))->with('success', 'Logged in to Administrator Console.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('admin/login'))->with('success', 'Administrator session safely terminated.');
    }
}
