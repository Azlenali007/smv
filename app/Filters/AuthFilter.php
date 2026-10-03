<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('is_logged_in') || $session->get('role') !== 'user') {
            return redirect()->to(site_url('login'))->with('error', 'Please log in to access your panel.');
        }

        // Verify account is still active
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($session->get('user_id'));
        if (!$user || $user['status'] !== 'active') {
            $session->destroy();
            return redirect()->to(site_url('login'))->with('error', 'Your account has been deactivated. Please contact support.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
