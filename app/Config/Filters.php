<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'admin_auth'    => \App\Filters\AdminFilter::class,
        'guest'         => \App\Filters\GuestFilter::class,
        'maintenance'   => \App\Filters\MaintenanceFilter::class,
    ];

    public array $globals = [
        'before' => [
            'invalidchars',
            'csrf' => [
                'except' => [
                    'api/*',
                    'webhook/*',
                    'install',
                    'install/*',
                ],
            ],
        ],
        'after' => [
            'secureheaders',
        ],
    ];

    public array $methods = [];

    public array $filters = [];
}
