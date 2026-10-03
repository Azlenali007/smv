<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;
use App\Models\PaymentGatewayModel;
use App\Models\PaymentTransactionModel;

class Wallet extends BaseController
{
    protected WalletModel $walletModel;
    protected WalletTransactionModel $txModel;
    protected PaymentGatewayModel $gatewayModel;
    protected PaymentTransactionModel $paymentTxModel;

    public function __construct()
    {
        $this->walletModel    = new WalletModel();
        $this->txModel        = new WalletTransactionModel();
        $this->gatewayModel   = new PaymentGatewayModel();
        $this->paymentTxModel = new PaymentTransactionModel();
    }

    public function index()
    {
        $userId = (int)session()->get('user_id');

        $wallet       = $this->walletModel->getWalletByUserId($userId);
        $gateways     = $this->gatewayModel->getActiveGateways();
        $transactions = $this->txModel->getUserTransactions($userId, 15, 0);
        $payments     = $this->paymentTxModel->getUserTransactions($userId);

        return $this->renderView('user/wallet', [
            'title'        => 'Wallet & Add Funds | ' . $this->data['site_name'],
            'wallet'       => $wallet,
            'gateways'     => $gateways,
            'transactions' => $transactions,
            'payments'     => $payments,
        ]);
    }

    public function initiateDeposit()
    {
        $userId    = (int)session()->get('user_id');
        $gatewayId = (int)$this->request->getPost('gateway_id');
        $amount    = (float)$this->request->getPost('amount');

        $gateway = $this->gatewayModel->find($gatewayId);
        if (!$gateway || !$gateway['is_active']) {
            return redirect()->back()->with('error', 'Selected payment method is currently unavailable.');
        }

        if ($amount < (float)$gateway['min_amount']) {
            return redirect()->back()->with('error', "Minimum deposit for {$gateway['name']} is {$gateway['currency_code']} " . number_format($gateway['min_amount'], 2));
        }

        if ($amount > (float)$gateway['max_amount']) {
            return redirect()->back()->with('error', "Maximum deposit for {$gateway['name']} is {$gateway['currency_code']} " . number_format($gateway['max_amount'], 2));
        }

        // Verify gateway has active credentials configured
        $credentials = $this->gatewayModel->getDecodedCredentials($gateway);
        if (empty($credentials)) {
            return redirect()->back()->with('error', "Payment gateway '{$gateway['name']}' is not yet configured with API credentials. Please contact administration.");
        }

        // Calculate converted amount to base INR
        $amountInInr = (float)$this->currencyService->convertToInr($amount, $gateway['currency_code']);
        $txReference = 'PAY-' . strtoupper(bin2hex(random_bytes(6)));

        // Create pending payment transaction record
        $this->paymentTxModel->insert([
            'user_id'               => $userId,
            'gateway_id'            => $gatewayId,
            'transaction_reference' => $txReference,
            'gateway_transaction_id'=> null,
            'amount'                => number_format($amount, 4, '.', ''),
            'currency'              => $gateway['currency_code'],
            'amount_in_inr'         => number_format($amountInInr, 4, '.', ''),
            'fee'                   => number_format(($amount * (float)$gateway['fee_percentage']) / 100, 4, '.', ''),
            'status'                => 'pending',
            'gateway_payload'       => json_encode(['initiated_at' => date('c'), 'user_id' => $userId]),
        ]);

        return redirect()->to(site_url('add-funds'))->with('info', "Payment initiated (Ref: {$txReference}). Pending server-to-server gateway verification.");
    }

    public function transactions()
    {
        $userId = (int)session()->get('user_id');
        $page   = max(1, (int)$this->request->getGet('page'));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $transactions = $this->txModel->getUserTransactions($userId, $limit, $offset);
        $totalCount   = $this->txModel->where('user_id', $userId)->countAllResults();

        return $this->renderView('user/wallet_transactions', [
            'title'        => 'Wallet Ledger | ' . $this->data['site_name'],
            'transactions' => $transactions,
            'currentPage'  => $page,
            'totalPages'   => ceil($totalCount / $limit),
        ]);
    }
}
