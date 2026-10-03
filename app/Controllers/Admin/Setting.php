<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\CurrencyModel;

class Setting extends BaseController
{
    protected SettingModel $settingModel;
    protected CurrencyModel $currencyModel;

    public function __construct()
    {
        $this->settingModel  = new SettingModel();
        $this->currencyModel = new CurrencyModel();
    }

    public function index()
    {
        $settings   = $this->settingModel->getAllAsAssociative();
        $currencies = $this->currencyModel->getActiveCurrencies();

        return $this->renderView('admin/settings', [
            'title'      => 'System & Dynamic Branding Settings | ' . $this->data['site_name'],
            'settings'   => $settings,
            'currencies' => $currencies,
        ]);
    }

    public function update()
    {
        $fields = [
            'site_name'           => 'string',
            'site_description'    => 'string',
            'contact_email'       => 'string',
            'default_currency'    => 'string',
            'maintenance_mode'    => 'string',
            'maintenance_message' => 'string',
            'min_deposit'         => 'string',
            'support_phone'       => 'string',
        ];

        foreach ($fields as $field => $type) {
            $val = $this->request->getPost($field);
            if ($field === 'maintenance_mode') {
                $val = $this->request->getPost('maintenance_mode') ? '1' : '0';
            }
            if ($val !== null) {
                $this->settingModel->setSetting($field, $val, $type);
            }
        }

        return redirect()->to(site_url('admin/settings'))->with('success', 'Dynamic branding and platform settings updated successfully.');
    }
}
