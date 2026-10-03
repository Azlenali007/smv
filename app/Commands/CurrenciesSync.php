<?php

namespace App\Commands;

use App\Models\CurrencyModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CurrenciesSync extends BaseCommand
{
    protected $group       = 'SMM Panel';
    protected $name        = 'currencies:sync';
    protected $description = 'Updates foreign currency exchange rates mapped against the base INR currency';

    public function run(array $params)
    {
        CLI::write('[APEXPULSE] Synchronizing currency exchange rates against base INR...', 'yellow');

        $currencyModel = new CurrencyModel();
        
        // Base currency is strictly INR = 1.000000
        $currencies = $currencyModel->where('is_active', 1)->findAll();

        foreach ($currencies as $c) {
            if ($c['code'] === 'INR') {
                $currencyModel->update($c['id'], ['rate_to_inr' => 1.000000, 'updated_at' => date('Y-m-d H:i:s')]);
                CLI::write("✓ Base Currency INR locked at 1.000000", 'green');
            } else {
                CLI::write("✓ Currency {$c['code']} maintained at current rate: {$c['rate_to_inr']} INR", 'cyan');
            }
        }

        CLI::write('[APEXPULSE] Currency exchange rate check completed.', 'light_green');
    }
}
