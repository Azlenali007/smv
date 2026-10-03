<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SettingModel;
use App\Models\CurrencyModel;
use App\Services\CurrencyService;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form', 'text', 'html'];
    protected array $data = [];
    protected SettingModel $settingModel;
    protected CurrencyService $currencyService;
    protected string $currentCurrency = 'INR';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->settingModel    = new SettingModel();
        $this->currencyService = new CurrencyService();

        // Load dynamic site settings
        $settings = $this->settingModel->getAllAsAssociative();
        $this->data['site_name']        = $settings['site_name'] ?? 'ApexPulse';
        $this->data['site_description'] = $settings['site_description'] ?? 'Premium Next-Generation SMM Growth Platform';
        $this->data['site_logo']        = $settings['site_logo'] ?? null;
        $this->data['site_favicon']     = $settings['site_favicon'] ?? null;
        $this->data['contact_email']    = $settings['contact_email'] ?? 'support@apexpulse.io';

        // Load active currencies for currency switcher
        $currModel = new CurrencyModel();
        $this->data['currencies'] = $currModel->getActiveCurrencies();

        // Determine current user display currency
        $session = session();
        if ($session->get('is_logged_in') && $session->get('currency_preference')) {
            $this->currentCurrency = $session->get('currency_preference');
        } else {
            $this->currentCurrency = $settings['default_currency'] ?? 'INR';
        }
        $this->data['current_currency'] = $this->currentCurrency;
        $this->data['currency_service'] = $this->currencyService;

        // Current user session data
        $this->data['user_session'] = [
            'is_logged_in' => $session->get('is_logged_in') ?? false,
            'user_id'      => $session->get('user_id'),
            'user_name'    => $session->get('user_name'),
            'user_email'   => $session->get('user_email'),
            'role'         => $session->get('role'),
        ];
    }

    /**
     * Render view with merged layout data
     */
    protected function renderView(string $viewPath, array $params = []): string
    {
        $merged = array_merge($this->data, $params);
        return view($viewPath, $merged);
    }
}
