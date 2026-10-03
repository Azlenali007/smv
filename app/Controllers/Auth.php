<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\WalletModel;

class Auth extends BaseController
{
    protected UserModel $userModel;
    protected WalletModel $walletModel;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->walletModel = new WalletModel();
    }

    public function login()
    {
        return $this->renderView('auth/login', [
            'title' => 'Sign In | ' . $this->data['site_name'],
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password combination.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Account is suspended or inactive. Please contact support.');
        }

        // Establish session
        session()->set([
            'is_logged_in'        => true,
            'user_id'             => (int)$user['id'],
            'user_name'           => $user['name'],
            'user_email'          => $user['email'],
            'role'                => $user['role'],
            'currency_preference' => $user['currency_preference'] ?? 'INR',
        ]);

        if ($user['role'] === 'admin') {
            return redirect()->to(site_url('admin/dashboard'))->with('success', 'Welcome back, Administrator ' . esc($user['name']));
        }

        return redirect()->to(site_url('dashboard'))->with('success', 'Welcome back, ' . esc($user['name']) . '!');
    }

    public function register()
    {
        return $this->renderView('auth/register', [
            'title' => 'Create Account | ' . $this->data['site_name'],
        ]);
    }

    public function attemptRegister()
    {
        $rules = [
            'name'                  => 'required|min_length[2]|max_length[100]',
            'email'                 => 'required|valid_email|is_unique[users.email]',
            'password'              => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
            'terms'                 => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'name'                => trim($this->request->getPost('name')),
            'email'               => trim(strtolower($this->request->getPost('email'))),
            'password'            => $this->request->getPost('password'),
            'role'                => 'user',
            'status'              => 'active',
            'currency_preference' => 'INR',
        ];

        $userId = $this->userModel->insert($userData);
        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Failed to create account. Please try again.');
        }

        // Automatically provision wallet for new user
        $this->walletModel->insert([
            'user_id' => $userId,
            'balance' => '0.0000',
        ]);

        // Auto login after registration
        session()->set([
            'is_logged_in'        => true,
            'user_id'             => (int)$userId,
            'user_name'           => $userData['name'],
            'user_email'          => $userData['email'],
            'role'                => 'user',
            'currency_preference' => 'INR',
        ]);

        return redirect()->to(site_url('dashboard'))->with('success', 'Account registered successfully! Welcome to ' . esc($this->data['site_name']));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'You have been safely signed out.');
    }

    public function forgotPassword()
    {
        return $this->renderView('auth/forgot_password', [
            'title' => 'Reset Password | ' . $this->data['site_name'],
        ]);
    }

    public function attemptForgotPassword()
    {
        $rules = ['email' => 'required|valid_email'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim($this->request->getPost('email'));
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user['id'], [
                'reset_token'      => $token,
                'reset_expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            ]);
            // In production, an email would be dispatched.
        }

        return redirect()->back()->with('success', 'If an account exists with this email address, password reset instructions have been generated.');
    }
}
