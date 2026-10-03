<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CurrencyModel;

class Profile extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = (int)session()->get('user_id');
        $user = $this->userModel->find($userId);

        $currModel = new CurrencyModel();
        $currencies = $currModel->getActiveCurrencies();

        return $this->renderView('user/profile', [
            'title'      => 'Account Settings & Profile | ' . $this->data['site_name'],
            'user'       => $user,
            'currencies' => $currencies,
        ]);
    }

    public function updateProfile()
    {
        $userId = (int)session()->get('user_id');

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name  = trim($this->request->getPost('name'));
        $email = trim(strtolower($this->request->getPost('email')));

        $this->userModel->update($userId, [
            'name'       => $name,
            'email'      => $email,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        session()->set([
            'user_name'  => $name,
            'user_email' => $email,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        $userId = (int)session()->get('user_id');
        $user   = $this->userModel->find($userId);

        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        if (!password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Your current password does not match.');
        }

        $this->userModel->update($userId, [
            'password_hash' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function setCurrency()
    {
        $userId   = (int)session()->get('user_id');
        $currency = strtoupper(trim((string)$this->request->getPost('currency')));

        $currModel = new CurrencyModel();
        $curr = $currModel->findByCode($currency);

        if (!$curr || !$curr['is_active']) {
            return redirect()->back()->with('error', 'Invalid currency selection.');
        }

        $this->userModel->update($userId, [
            'currency_preference' => $currency,
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        session()->set('currency_preference', $currency);

        return redirect()->back()->with('success', "Display currency updated to {$currency} ({$curr['symbol']}).");
    }
}
