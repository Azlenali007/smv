<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public string $baseURL = 'http://localhost:8080/';
    public array $allowedHostnames = [];
    public string $indexPage = '';
    public string $uriProtocol = 'REQUEST_URI';
    public string $defaultLocale = 'en';
    public bool $negotiateLocale = false;
    public array $supportedLocales = ['en'];
    public string $appTimezone = 'UTC';
    public string $charset = 'UTF-8';
    public bool $forceGlobalSecureRequests = false;
    public string $sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler';
    public string $sessionCookieName = 'ci_session';
    public int $sessionExpiration = 7200;
    public string $sessionSavePath = WRITEPATH . 'session';
    public bool $sessionMatchIP = false;
    public int $sessionTimeToUpdate = 300;
    public bool $sessionRegenerateDestroy = false;
    public ?string $sessionDBGroup = null;
    public string $cookiePrefix = '';
    public string $cookieDomain = '';
    public string $cookiePath = '/';
    public bool $cookieSecure = false;
    public bool $cookieHTTPOnly = true;
    public ?string $cookieSameSite = 'Lax';
    public array $proxyIPs = [];
    public bool $CSPEnabled = false;

    // SMM Panel Specific Configuration
    public string $siteName = 'ApexPulse';
    public string $baseCurrency = 'INR';

    public function __construct()
    {
        parent::__construct();

        // Ensure session save directory exists
        if (!is_dir($this->sessionSavePath)) {
            @mkdir($this->sessionSavePath, 0755, true);
        }

        // Dynamically detect base URL when accessing via HTTP/HTTPS on real domain/subdomain
        if (isset($_SERVER['HTTP_HOST'])) {
            $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                       (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                       (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            $scheme = $isHttps ? 'https://' : 'http://';
            
            // Detect script subdirectory if installed in subfolder
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $subDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            if ($subDir === '.' || $subDir === '/public' || $subDir === '') {
                $subDir = '';
            }
            $detectedBase = $scheme . $_SERVER['HTTP_HOST'] . $subDir . '/';

            // Use detectedBase if baseURL is empty or still default localhost:8080
            if (empty($this->baseURL) || str_contains($this->baseURL, 'localhost:8080')) {
                $this->baseURL = $detectedBase;
            }
        }
    }
}
