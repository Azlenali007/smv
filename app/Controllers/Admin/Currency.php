<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CurrencyModel;

class Currency extends BaseController
{
    protected CurrencyModel $currencyModel;

    public function __construct()
    {
        $this->currencyModel = new CurrencyModel();
    }

    public function index()
    {
        $currencies = $this->currencyModel->orderBy('id', 'ASC')->findAll();

        return $this->renderView('admin/currencies', [
            'title'      => 'Multi-Currency Management | ' . $this->data['site_name'],
            'currencies' => $currencies,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'              => 'required|min_length[2]|max_length[60]',
            'code'              => 'required|min_length[2]|max_length[10]|is_unique[currencies.code]',
            'symbol'            => 'required|max_length[10]',
            'rate_to_inr'       => 'required|numeric|greater_than[0]',
            'decimal_precision' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[4]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code = strtoupper(trim($this->request->getPost('code')));

        $this->currencyModel->insert([
            'name'              => trim($this->request->getPost('name')),
            'code'              => $code,
            'symbol'            => trim($this->request->getPost('symbol')),
            'rate_to_inr'       => number_format((float)$this->request->getPost('rate_to_inr'), 6, '.', ''),
            'decimal_precision' => (int)$this->request->getPost('decimal_precision'),
            'is_default'        => 0,
            'is_active'         => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(site_url('admin/currencies'))->with('success', "Currency {$code} configured successfully.");
    }

    public function update(int $currencyId)
    {
        $curr = $this->currencyModel->find($currencyId);
        if (!$curr) {
            return redirect()->back()->with('error', 'Currency not found.');
        }

        $rate = (float)$this->request->getPost('rate_to_inr');
        if ($curr['code'] === 'INR') {
            $rate = 1.000000; // Base currency is always exactly 1.0
        }

        $this->currencyModel->update($currencyId, [
            'name'              => trim($this->request->getPost('name')),
            'symbol'            => trim($this->request->getPost('symbol')),
            'rate_to_inr'       => number_format($rate, 6, '.', ''),
            'decimal_precision' => (int)$this->request->getPost('decimal_precision'),
            'is_active'         => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/currencies'))->with('success', "Exchange rate and settings updated for {$curr['code']}.");
    }

    public function setDefault(int $currencyId)
    {
        $curr = $this->currencyModel->find($currencyId);
        if (!$curr) {
            return redirect()->back()->with('error', 'Currency not found.');
        }

        // Reset all defaults
        $this->currencyModel->where('is_default', 1)->set(['is_default' => 0])->update();
        $this->currencyModel->update($currencyId, ['is_default' => 1, 'is_active' => 1]);

        return redirect()->to(site_url('admin/currencies'))->with('success', "Default display currency set to {$curr['code']}.");
    }
}
