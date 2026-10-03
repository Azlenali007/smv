<?php

namespace App\Commands;

use App\Models\ProviderModel;
use App\Services\SmmProviderService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProvidersBalance extends BaseCommand
{
    protected $group       = 'SMM Panel';
    protected $name        = 'providers:balance';
    protected $description = 'Polls remote SMM providers to update their latest account balances';

    public function run(array $params)
    {
        CLI::write('[APEXPULSE] Querying upstream SMM provider account balances...', 'yellow');

        $providerModel = new ProviderModel();
        $providerService = new SmmProviderService();

        $providers = $providerModel->where('status', 'active')->findAll();

        if (empty($providers)) {
            CLI::write('[APEXPULSE] No active API providers registered.', 'white');
            return;
        }

        foreach ($providers as $p) {
            CLI::write("Querying provider '{$p['name']}' ({$p['api_url']})...", 'cyan');
            $bal = $providerService->getBalance($p['id']);

            if ($bal['success']) {
                $providerModel->update($p['id'], [
                    'balance'      => (float)$bal['balance'],
                    'currency'     => $bal['currency'] ?? $p['currency'],
                    'last_sync_at' => date('Y-m-d H:i:s'),
                ]);
                CLI::write("✓ {$p['name']} balance: {$bal['currency']} " . number_format($bal['balance'], 2), 'green');
            } else {
                CLI::error("✗ Failed to query {$p['name']}: " . ($bal['error'] ?? 'Network timeout'));
            }
        }

        CLI::write('[APEXPULSE] Provider balance check completed.', 'light_green');
    }
}
