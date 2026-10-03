<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;
use App\Models\OrderModel;
use App\Services\WalletService;

class User extends BaseController
{
    protected UserModel $userModel;
    protected WalletModel $walletModel;
    protected WalletTransactionModel $txModel;
    protected OrderModel $orderModel;
    protected WalletService $walletService;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->walletModel   = new WalletModel();
        $this->txModel       = new WalletTransactionModel();
        $this->orderModel    = new OrderModel();
        $this->walletService = new WalletService();
    }

    public function index()
    {
        $search = $this->request->getGet('search') ? trim($this->request->getGet('search')) : null;
        $status = $this->request->getGet('status') ? trim($this->request->getGet('status')) : null;

        $builder = $this->userModel->select('users.*, wallets.balance as wallet_balance')
                                   ->join('wallets', 'wallets.user_id = users.id', 'left')
                                   ->where('users.role', 'user');

        if ($search) {
            $builder->groupStart()
                    ->like('users.name', $search)
                    ->orLike('users.email', $search)
                    ->orLike('users.id', $search)
                    ->groupEnd();
        }

        if ($status) {
            $builder->where('users.status', $status);
        }

        $users = $builder->orderBy('users.id', 'DESC')->findAll();

        return $this->renderView('admin/users', [
            'title'  => 'User Management | ' . $this->data['site_name'],
            'users'  => $users,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(int $userId)
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User not found.');
        }

        $wallet       = $this->walletModel->getWalletByUserId($userId);
        $orders       = $this->orderModel->getUserOrders($userId, null, null, 15, 0);
        $transactions = $this->txModel->getUserTransactions($userId, 15, 0);
        $stats        = $this->orderModel->getUserStats($userId);

        return $this->renderView('admin/user_detail', [
            'title'        => "User #{$userId}: " . esc($user['name']),
            'user'         => $user,
            'wallet'       => $wallet,
            'orders'       => $orders,
            'transactions' => $transactions,
            'stats'        => $stats,
        ]);
    }

    public function toggleStatus(int $userId)
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
        $this->userModel->update($userId, [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', "User account status switched to {$newStatus}.");
    }

    public function adjustWallet(int $userId)
    {
        $adminId = (int)session()->get('user_id');
        $amount  = (float)$this->request->getPost('amount');
        $type    = $this->request->getPost('type') ?? 'credit'; // 'credit' or 'debit'
        $reason  = trim((string)$this->request->getPost('reason'));

        if (empty($reason)) {
            return redirect()->back()->with('error', 'A specific reason/audit remark must be provided.');
        }

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Adjustment amount must be greater than zero.');
        }

        $delta = ($type === 'debit') ? (-1.0 * $amount) : $amount;

        $res = $this->walletService->manualAdjustment($userId, $adminId, $delta, $reason);

        if (!$res['success']) {
            return redirect()->back()->with('error', $res['error']);
        }

        return redirect()->back()->with('success', "Wallet adjusted successfully! New balance: ₹{$res['new_balance']}");
    }
}
