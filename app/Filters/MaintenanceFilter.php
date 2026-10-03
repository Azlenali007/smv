<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if admin is logged in - authorized admin access remains available
        $session = session();
        if ($session->get('is_logged_in') && $session->get('role') === 'admin') {
            return;
        }

        // Check maintenance mode in site_settings
        $settingModel = new \App\Models\SettingModel();
        $isMaintenance = $settingModel->getSetting('maintenance_mode', '0');

        if ($isMaintenance === '1' || $isMaintenance === 1) {
            $siteName = $settingModel->getSetting('site_name', 'ApexPulse');
            $customMessage = $settingModel->getSetting('maintenance_message', 'We are currently undergoing scheduled maintenance to upgrade our platform. We will be back online shortly.');
            
            $response = service('response');
            $response->setStatusCode(503);
            $response->setBody(view('errors/html/maintenance', [
                'site_name' => $siteName,
                'message'   => $customMessage,
            ]));
            return $response;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
