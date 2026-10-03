<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Exceptions extends BaseConfig
{
    public bool $log = true;
    public bool $ignoreReportsOnWeb = false;
    public array $ignoreCodes = [404];
    public string $errorViewPath = APPPATH . 'Views/errors';
}
