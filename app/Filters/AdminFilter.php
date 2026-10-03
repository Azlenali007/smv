<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('is_logged_in') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('admin/login'))->with('error', 'Administrator authorization required.');
        }

        // Verify admin record
        $userModel = new \App\Models\UserModel();
        $admin = $userModel->find($session->get('user_id'));
        if (!$admin || $admin['role'] !== 'admin' || $admin['status'] !== 'active') {
            $session->destroy();
            return redirect()->to(site_url('admin/login'))->with('error', 'Administrator account invalid or inactive.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
