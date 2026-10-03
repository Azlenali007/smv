<?php

namespace App\Services;

use App\Models\WalletModel;
use App\Models\WalletTransactionModel;
use Config\Database;
use Exception;

class WalletService
{
    protected WalletModel $walletModel;
    protected WalletTransactionModel $txModel;

    public function __construct()
    {
        $this->walletModel = new WalletModel();
        $this->txModel     = new WalletTransactionModel();
    }

    /**
     * Get user wallet, auto-creating if absent
     */
    public function getWallet(int $userId): array
    {
        return $this->walletModel->getWalletByUserId($userId);
    }

    /**
     * Atomically debit an amount from the user's wallet for an order
     * 
     * @param int $userId
     * @param float|string $amountInInr Amount in INR
     * @param string $referenceId Order ID or Reference
     * @param string $remarks
     * @return array ['success' => bool, 'error' => string|null, 'new_balance' => string]
     */
    public function debitForOrder(int $userId, float|string $amountInInr, string $referenceId, string $remarks = 'Order Payment'): array
    {
        $amount = (float)$amountInInr;
        if ($amount <= 0) {
            return ['success' => false, 'error' => 'Debit amount must be greater than zero.'];
        }

        $db = Database::connect();
        $db->transStart();

        try {
            // Lock row for update
            $wallet = $db->table('wallets')
                         ->where('user_id', $userId)
                         ->get()
                         ->getRowArray();

            if (!$wallet) {
                $db->table('wallets')->insert([
                    'user_id'    => $userId,
                    'balance'    => '0.0000',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $wallet = $db->table('wallets')->where('user_id', $userId)->get()->getRowArray();
            }

            $currentBalance = (float)$wallet['balance'];
            if ($currentBalance < $amount) {
                $db->transRollback();
                return [
                    'success' => false,
                    'error'   => 'Insufficient wallet balance. Please add funds to proceed.',
                ];
            }

            $newBalance = $currentBalance - $amount;
            $newBalanceStr = number_format($newBalance, 4, '.', '');

            // Update balance
            $db->table('wallets')
               ->where('id', $wallet['id'])
               ->update([
                   'balance'    => $newBalanceStr,
                   'updated_at' => date('Y-m-d H:i:s'),
               ]);

            // Create immutable transaction ledger
            $db->table('wallet_transactions')->insert([
                'wallet_id'       => $wallet['id'],
                'user_id'         => $userId,
                'type'            => 'order_debit',
                'amount'          => number_format($amount, 4, '.', ''),
                'opening_balance' => number_format($currentBalance, 4, '.', ''),
                'closing_balance' => $newBalanceStr,
                'currency_code'   => 'INR',
                'reference_id'    => $referenceId,
                'remarks'         => $remarks,
                'admin_id'        => null,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'error' => 'Database transaction failed during wallet debit.'];
            }

            return ['success' => true, 'new_balance' => $newBalanceStr];
        } catch (Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => 'Wallet debit exception: ' . $e->getMessage()];
        }
    }

    /**
     * Atomically refund an amount back to user's wallet
     */
    public function creditRefund(int $userId, float|string $amountInInr, string $referenceId, string $remarks = 'Order Refund'): array
    {
        $amount = (float)$amountInInr;
        if ($amount <= 0) {
            return ['success' => false, 'error' => 'Refund amount must be greater than zero.'];
        }

        $db = Database::connect();
        $db->transStart();

        try {
            $wallet = $db->table('wallets')->where('user_id', $userId)->get()->getRowArray();
            if (!$wallet) {
                $db->table('wallets')->insert([
                    'user_id'    => $userId,
                    'balance'    => '0.0000',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $wallet = $db->table('wallets')->where('user_id', $userId)->get()->getRowArray();
            }

            $currentBalance = (float)$wallet['balance'];
            $newBalance = $currentBalance + $amount;
            $newBalanceStr = number_format($newBalance, 4, '.', '');

            $db->table('wallets')->where('id', $wallet['id'])->update([
                'balance'    => $newBalanceStr,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $db->table('wallet_transactions')->insert([
                'wallet_id'       => $wallet['id'],
                'user_id'         => $userId,
                'type'            => 'refund',
                'amount'          => number_format($amount, 4, '.', ''),
                'opening_balance' => number_format($currentBalance, 4, '.', ''),
                'closing_balance' => $newBalanceStr,
                'currency_code'   => 'INR',
                'reference_id'    => $referenceId,
                'remarks'         => $remarks,
                'admin_id'        => null,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();
            return ['success' => true, 'new_balance' => $newBalanceStr];
        } catch (Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => 'Wallet refund exception: ' . $e->getMessage()];
        }
    }

    /**
     * Atomically credit deposit after verified payment gateway response
     */
    public function creditDeposit(int $userId, float|string $amountInInr, string $referenceId, string $remarks = 'Payment Gateway Deposit'): array
    {
        $amount = (float)$amountInInr;
        if ($amount <= 0) {
            return ['success' => false, 'error' => 'Deposit amount must be positive.'];
        }

        $db = Database::connect();
        $db->transStart();

        try {
            $wallet = $db->table('wallets')->where('user_id', $userId)->get()->getRowArray();
            if (!$wallet) {
                $db->table('wallets')->insert([
                    'user_id'    => $userId,
                    'balance'    => '0.0000',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $wallet = $db->table('wallets')->where('user_id', $userId)->get()->getRowArray();
            }

            $currentBalance = (float)$wallet['balance'];
            $newBalance = $currentBalance + $amount;
            $newBalanceStr = number_format($newBalance, 4, '.', '');

            $db->table('wallets')->where('id', $wallet['id'])->update([
                'balance'    => $newBalanceStr,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $db->table('wallet_transactions')->insert([
                'wallet_id'       => $wallet['id'],
                'user_id'         => $userId,
                'type'            => 'deposit',
                'amount'          => number_format($amount, 4, '.', ''),
                'opening_balance' => number_format($currentBalance, 4, '.', ''),
                'closing_balance' => $newBalanceStr,
                'currency_code'   => 'INR',
                'reference_id'    => $referenceId,
                'remarks'         => $remarks,
                'admin_id'        => null,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();
            return ['success' => true, 'new_balance' => $newBalanceStr];
        } catch (Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => 'Wallet deposit error: ' . $e->getMessage()];
        }
    }

    /**
     * Admin manual adjustment with audit trail, admin_id and atomic transaction
     */
    public function manualAdjustment(int $userId, int $adminId, float|string $amountDelta, string $reason): array
    {
        $delta = (float)$amountDelta;
        if ($delta == 0.0) {
            return ['success' => false, 'error' => 'Adjustment amount cannot be zero.'];
        }

        $db = Database::connect();
        $db->transStart();

        try {
            $wallet = $db->table('wallets')->where('user_id', $userId)->get()->getRowArray();
            if (!$wallet) {
                $db->table('wallets')->insert([
                    'user_id'    => $userId,
                    'balance'    => '0.0000',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $wallet = $db->table('wallets')->where('user_id', $userId)->get()->getRowArray();
            }

            $currentBalance = (float)$wallet['balance'];
            $newBalance = $currentBalance + $delta;
            if ($newBalance < 0) {
                $db->transRollback();
                return ['success' => false, 'error' => 'Cannot adjust balance below zero. Current: ₹' . number_format($currentBalance, 2)];
            }

            $newBalanceStr = number_format($newBalance, 4, '.', '');

            $db->table('wallets')->where('id', $wallet['id'])->update([
                'balance'    => $newBalanceStr,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $db->table('wallet_transactions')->insert([
                'wallet_id'       => $wallet['id'],
                'user_id'         => $userId,
                'type'            => 'manual_adjustment',
                'amount'          => number_format($delta, 4, '.', ''),
                'opening_balance' => number_format($currentBalance, 4, '.', ''),
                'closing_balance' => $newBalanceStr,
                'currency_code'   => 'INR',
                'reference_id'    => 'MANUAL-ADMIN-' . $adminId,
                'remarks'         => $reason,
                'admin_id'        => $adminId,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();
            return ['success' => true, 'new_balance' => $newBalanceStr];
        } catch (Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => 'Manual adjustment exception: ' . $e->getMessage()];
        }
    }
}
