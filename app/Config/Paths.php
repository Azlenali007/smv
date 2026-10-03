<?php

namespace Config;

/**
 * Paths Configuration for CodeIgniter 4
 */
class Paths
{
    public string $systemDirectory;
    public string $appDirectory = __DIR__ . '/..';
    public string $writableDirectory;
    public string $testsDirectory = __DIR__ . '/../../tests';
    public string $viewDirectory = __DIR__ . '/../Views';

    public function __construct()
    {
        // Support both direct system/ directory and vendor/codeigniter4/framework/system
        if (is_dir(__DIR__ . '/../../system')) {
            $this->systemDirectory = realpath(__DIR__ . '/../../system') ?: (__DIR__ . '/../../system');
        } else {
            $this->systemDirectory = __DIR__ . '/../../vendor/codeigniter4/framework/system';
        }

        $writablePath = realpath(__DIR__ . '/../../writable') ?: (__DIR__ . '/../../writable');
        $this->writableDirectory = rtrim($writablePath, '\\/') . DIRECTORY_SEPARATOR;

        // Ensure critical storage subdirectories exist
        foreach (['cache', 'session', 'logs', 'debugbar'] as $sub) {
            $dir = $this->writableDirectory . $sub;
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
    }
}
