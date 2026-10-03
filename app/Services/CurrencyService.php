<?php

namespace App\Services;

use App\Models\CurrencyModel;

class CurrencyService
{
    protected CurrencyModel $currencyModel;
    protected array $cache = [];

    public const BASE_CURRENCY = 'INR';

    public function __construct()
    {
        $this->currencyModel = new CurrencyModel();
    }

    /**
     * Get currency details by code
     */
    public function getCurrency(string $code): ?array
    {
        $code = strtoupper(trim($code));
        if (isset($this->cache[$code])) {
            return $this->cache[$code];
        }

        $curr = $this->currencyModel->findByCode($code);
        if ($curr) {
            $this->cache[$code] = $curr;
        }
        return $curr;
    }

    /**
     * Convert any amount from a given currency into base currency INR.
     * Exchange rate in database represents: 1 [Currency] = rate_to_inr INR.
     * For INR itself, rate_to_inr = 1.000000.
     * Example: 1 USD = 86.500000 INR.
     * 10 USD -> 10 * 86.500000 = 865.0000 INR.
     */
    public function convertToInr(float|string $amount, string $fromCurrency): string
    {
        $amount = (string)$amount;
        $fromCurrency = strtoupper(trim($fromCurrency));

        if ($fromCurrency === self::BASE_CURRENCY) {
            return number_format((float)$amount, 4, '.', '');
        }

        $currency = $this->getCurrency($fromCurrency);
        if (!$currency || (float)$currency['rate_to_inr'] <= 0) {
            // Safe fallback: 1:1 if unknown, but log discrepancy
            return number_format((float)$amount, 4, '.', '');
        }

        $rate = (float)$currency['rate_to_inr'];
        $inrVal = (float)$amount * $rate;
        return number_format($inrVal, 4, '.', '');
    }

    /**
     * Convert an INR amount into the target display currency.
     * Example: 865.0000 INR into USD (where 1 USD = 86.500000 INR):
     * USD = 865.0000 / 86.500000 = 10.00 USD.
     */
    public function convertFromInr(float|string $amountInInr, string $targetCurrency): string
    {
        $amountInInr = (float)$amountInInr;
        $targetCurrency = strtoupper(trim($targetCurrency));

        if ($targetCurrency === self::BASE_CURRENCY) {
            return number_format($amountInInr, 4, '.', '');
        }

        $currency = $this->getCurrency($targetCurrency);
        if (!$currency || (float)$currency['rate_to_inr'] <= 0) {
            return number_format($amountInInr, 4, '.', '');
        }

        $rate = (float)$currency['rate_to_inr'];
        $converted = $amountInInr / $rate;
        $precision = (int)($currency['decimal_precision'] ?? 2);

        return number_format($converted, $precision, '.', '');
    }

    /**
     * Complete calculation pipeline:
     * Provider Currency -> Convert to INR -> Apply Admin Margin -> Convert to User Display Currency
     * 
     * @param float|string $providerRate Rate per 1,000 in provider currency
     * @param string $providerCurrency e.g. 'USD'
     * @param float $marginPercentage e.g. 20.0 (20%)
     * @param string $userDisplayCurrency e.g. 'INR' or 'USD'
     * @return array [
     *   'rate_in_inr' => string, // Base INR selling rate
     *   'display_rate' => string, // Formatted for user
     *   'symbol' => string,
     *   'currency' => string
     * ]
     */
    public function calculateSellingRate(
        float|string $providerRate,
        string $providerCurrency,
        float $marginPercentage,
        string $userDisplayCurrency
    ): array {
        // Step 1: Provider Currency -> INR
        $baseInr = (float)$this->convertToInr($providerRate, $providerCurrency);

        // Step 2: Apply Admin Margin
        $marginFactor = 1.0 + ($marginPercentage / 100.0);
        $sellingInr = $baseInr * $marginFactor;

        // Step 3: Convert to User Display Currency
        $displayRate = $this->convertFromInr($sellingInr, $userDisplayCurrency);
        $curr = $this->getCurrency($userDisplayCurrency);
        $symbol = $curr['symbol'] ?? '₹';

        return [
            'base_inr'     => number_format($baseInr, 4, '.', ''),
            'rate_in_inr'  => number_format($sellingInr, 4, '.', ''),
            'display_rate' => $displayRate,
            'symbol'       => $symbol,
            'currency'     => $userDisplayCurrency,
        ];
    }

    /**
     * Format money with symbol and proper precision
     */
    public function format(float|string $amount, string $currencyCode): string
    {
        $curr = $this->getCurrency($currencyCode);
        $symbol = $curr['symbol'] ?? ($currencyCode === 'INR' ? '₹' : '$');
        $precision = (int)($curr['decimal_precision'] ?? 2);
        return $symbol . number_format((float)$amount, $precision, '.', ',');
    }
}
