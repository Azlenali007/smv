<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentGatewayModel;
use App\Models\PaymentTransactionModel;
use App\Services\WalletService;

class Payment extends BaseController
{
    protected PaymentGatewayModel $gatewayModel;
    protected PaymentTransactionModel $paymentTxModel;
    protected WalletService $walletService;

    public function __construct()
    {
        $this->gatewayModel   = new PaymentGatewayModel();
        $this->paymentTxModel = new PaymentTransactionModel();
        $this->walletService  = new WalletService();
    }

    public function index()
    {
        $gateways     = $this->gatewayModel->findAll();
        $transactions = $this->paymentTxModel->getAllTransactions();

        return $this->renderView('admin/payments', [
            'title'        => 'Payment Gateways & Ledger | ' . $this->data['site_name'],
            'gateways'     => $gateways,
            'transactions' => $transactions,
        ]);
    }

    public function updateGateway(int $gatewayId)
    {
        $gateway = $this->gatewayModel->find($gatewayId);
        if (!$gateway) {
            return redirect()->back()->with('error', 'Gateway not found.');
        }

        $credentialsRaw = $this->request->getPost('credentials');
        // Validate JSON format if provided
        if (!empty($credentialsRaw)) {
            $test = json_decode($credentialsRaw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->withInput()->with('error', 'Credentials must be valid JSON format.');
            }
        }

        $this->gatewayModel->update($gatewayId, [
            'name'           => trim($this->request->getPost('name')),
            'min_amount'     => number_format((float)$this->request->getPost('min_amount'), 4, '.', ''),
            'max_amount'     => number_format((float)$this->request->getPost('max_amount'), 4, '.', ''),
            'fee_percentage' => (float)$this->request->getPost('fee_percentage'),
            'currency_code'  => strtoupper(trim((string)$this->request->getPost('currency_code'))),
            'credentials'    => $credentialsRaw ?: null,
            'instructions'   => trim((string)$this->request->getPost('instructions')),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/payments'))->with('success', "Gateway {$gateway['name']} configuration updated.");
    }

    public function transactions()
    {
        $transactions = $this->paymentTxModel->getAllTransactions();

        return $this->renderView('admin/payment_transactions', [
            'title'        => 'Payment Audit Ledger | ' . $this->data['site_name'],
            'transactions' => $transactions,
        ]);
    }
}
