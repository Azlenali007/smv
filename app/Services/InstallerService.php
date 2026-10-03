<?php

namespace App\Services;

use Exception;
use PDO;
use PDOException;

class InstallerService
{
    protected string $lockFile;
    protected string $installedLockFile;

    public function __construct()
    {
        $this->lockFile = WRITEPATH . 'install.lock';
        $this->installedLockFile = WRITEPATH . 'installed.lock';
    }

    /**
     * Check if the application is already locked/installed
     */
    public function isInstalled(): bool
    {
        return file_exists($this->lockFile) || file_exists($this->installedLockFile);
    }

    /**
     * Create the installation lock file to prevent re-installation
     */
    public function createLockFile(array $metadata = []): bool
    {
        $dir = dirname($this->lockFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = json_encode(array_merge([
            'installed_at' => date('Y-m-d H:i:s'),
            'php_version'  => PHP_VERSION,
            'app_version'  => '1.0.0',
            'locked'       => true,
        ], $metadata), JSON_PRETTY_PRINT);

        $res1 = file_put_contents($this->lockFile, $content);
        $res2 = file_put_contents($this->installedLockFile, $content);

        return (bool) ($res1 && $res2);
    }

    /**
     * Check system hosting requirements
     */
    public function checkRequirements(): array
    {
        $checks = [];
        $allPassed = true;

        // 1. PHP Version (>= 8.2 required)
        $currentPhp = PHP_VERSION;
        $phpPassed = version_compare($currentPhp, '8.2.0', '>=');
        $checks['php_version'] = [
            'title'       => 'PHP Version (>= 8.2 required)',
            'current'     => $currentPhp,
            'status'      => $phpPassed ? 'passed' : 'failed',
            'critical'    => true,
            'description' => $phpPassed ? 'Supported PHP version detected.' : 'Please upgrade PHP to 8.2 or newer.',
        ];
        if (!$phpPassed) $allPassed = false;

        // 2. Required PHP Extensions
        $requiredExtensions = [
            'pdo'        => ['title' => 'PDO Extension', 'critical' => true],
            'pdo_mysql'  => ['title' => 'PDO MySQL Driver', 'critical' => true],
            'curl'       => ['title' => 'cURL Extension', 'critical' => true],
            'openssl'    => ['title' => 'OpenSSL Extension', 'critical' => true],
            'mbstring'   => ['title' => 'Mbstring Extension', 'critical' => true],
            'json'       => ['title' => 'JSON Extension', 'critical' => true],
            'fileinfo'   => ['title' => 'Fileinfo Extension', 'critical' => false],
            'intl'       => ['title' => 'Intl Extension', 'critical' => false],
            'xml'        => ['title' => 'XML Extension', 'critical' => false],
        ];

        foreach ($requiredExtensions as $ext => $info) {
            $loaded = extension_loaded($ext);
            $checks['ext_' . $ext] = [
                'title'       => $info['title'],
                'current'     => $loaded ? 'Installed' : 'Missing',
                'status'      => $loaded ? 'passed' : ($info['critical'] ? 'failed' : 'warning'),
                'critical'    => $info['critical'],
                'description' => $loaded ? 'Extension is available.' : ($info['critical'] ? 'Required for core backend operations.' : 'Recommended for internationalization or full parsing.'),
            ];
            if (!$loaded && $info['critical']) {
                $allPassed = false;
            }
        }

        // 3. Writable Directories
        $writableDirs = [
            'writable'         => WRITEPATH,
            'writable/cache'   => WRITEPATH . 'cache',
            'writable/session' => WRITEPATH . 'session',
            'writable/logs'    => WRITEPATH . 'logs',
        ];

        foreach ($writableDirs as $name => $path) {
            if (!is_dir($path)) {
                @mkdir($path, 0755, true);
            }
            $isWritable = is_writable($path);
            $checks['dir_' . md5($name)] = [
                'title'       => "Directory Writable ({$name})",
                'current'     => $isWritable ? 'Writable' : 'Not Writable',
                'status'      => $isWritable ? 'passed' : 'failed',
                'critical'    => true,
                'description' => $isWritable ? 'Write permissions verified.' : 'Permissions need to be set to 755 or 775.',
            ];
            if (!$isWritable) {
                $allPassed = false;
            }
        }

        return [
            'all_passed' => $allPassed,
            'checks'     => $checks,
        ];
    }

    /**
     * Test real connection to MySQL server
     */
    public function testDatabaseConnection(string $host, int|string $port, string $database, string $username, string $password): array
    {
        $port = (int)$port ?: 3306;

        try {
            // First attempt connecting to server without db to check credentials
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT            => 5,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Now check if database exists or create it if permitted
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :db");
            $stmt->execute([':db' => $database]);
            $dbExists = (bool) $stmt->fetch();

            if (!$dbExists) {
                try {
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $dbExists = true;
                } catch (Exception $e) {
                    // Cannot auto-create, prompt user
                    return [
                        'success' => false,
                        'error'   => "Connected to MySQL server, but database '{$database}' does not exist and your MySQL user has insufficient privileges to CREATE DATABASE. Please create it in cPanel/phpMyAdmin.",
                    ];
                }
            }

            return [
                'success'   => true,
                'message'   => "Successfully connected to MySQL server and selected database '{$database}'.",
                'db_exists' => $dbExists,
            ];
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            // Sanitize sensitive credentials from error message
            $msg = preg_replace('/password=[^; ]+/i', 'password=***', $msg);
            $msg = str_replace($password, '***', $msg);

            return [
                'success' => false,
                'error'   => 'Database Connection Error: ' . $msg,
            ];
        }
    }

    /**
     * Write .env file with production configuration
     */
    public function saveEnvironmentConfig(array $dbConfig, array $appConfig): bool
    {
        $envFile = ROOTPATH . '.env';

        $encryptionKey = bin2hex(random_bytes(16));
        $siteUrl = rtrim($appConfig['site_url'] ?? 'http://localhost:8080/', '/') . '/';
        $env = $appConfig['app_env'] ?? 'production';
        $timezone = $appConfig['timezone'] ?? 'UTC';
        $baseCurrency = $appConfig['default_currency'] ?? 'INR';

        $dbHost = $dbConfig['host'] ?? 'localhost';
        $dbPort = (int)($dbConfig['port'] ?? 3306);
        $dbName = $dbConfig['database'] ?? 'smm_panel';
        $dbUser = $dbConfig['username'] ?? 'root';
        $dbPass = $dbConfig['password'] ?? '';

        $content = <<<ENV
#--------------------------------------------------------------------
# APEXPULSE SMM PANEL - GENERATED BY WEB INSTALLER
#--------------------------------------------------------------------

CI_ENVIRONMENT = {$env}

# Base Application Settings
app.baseURL = '{$siteUrl}'
app.forceGlobalSecureRequests = false
app.appTimezone = '{$timezone}'
app.baseCurrency = '{$baseCurrency}'
app.siteName = '{$appConfig['site_name']}'

# Session and Security
app.sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler'
app.sessionCookieName = 'ci_session'
app.sessionExpiration = 7200
app.sessionSavePath = null
app.sessionMatchIP = false

security.csrfProtection = 'cookie'
security.tokenRandomize = true
security.tokenName = 'csrf_token_name'
security.headerName = 'X-CSRF-TOKEN'
security.cookieName = 'csrf_cookie_name'
security.expires = 7200
security.regenerate = true
security.redirect = true
security.samesite = 'Lax'

# Encryption Key
encryption.key = '{$encryptionKey}'

# Database Connection (MySQL 8.x)
database.default.hostname = {$dbHost}
database.default.database = {$dbName}
database.default.username = {$dbUser}
database.default.password = '{$dbPass}'
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = {$dbPort}
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci
database.default.strictOn = true

ENV;

        return (bool) file_put_contents($envFile, $content);
    }

    /**
     * Execute migrations and provision the default administrator
     */
    public function installDatabaseAndAdmin(array $dbConfig, array $appConfig, array $adminData): array
    {
        $port = (int)($dbConfig['port'] ?? 3306);
        $host = $dbConfig['host'] ?? 'localhost';
        $dbName = $dbConfig['database'] ?? 'smm_panel';
        $user = $dbConfig['username'] ?? 'root';
        $pass = $dbConfig['password'] ?? '';

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Execute SQL table definitions directly to ensure all core tables exist in the exact target database
            $schemaSql = $this->getCoreDatabaseSchema();
            $pdo->exec($schemaSql);

            // Seed required default configuration records:
            // 1. Default Currencies (Strict INR base)
            $stmt = $pdo->prepare("INSERT INTO `currencies` (`name`, `code`, `symbol`, `rate_to_inr`, `decimal_precision`, `is_default`, `is_active`, `created_at`, `updated_at`) 
                VALUES 
                ('Indian Rupee', 'INR', '₹', 1.000000, 2, 1, 1, NOW(), NOW()),
                ('US Dollar', 'USD', '$', 86.500000, 2, 0, 1, NOW(), NOW()),
                ('Euro', 'EUR', '€', 94.200000, 2, 0, 1, NOW(), NOW()),
                ('British Pound', 'GBP', '£', 110.800000, 2, 0, 1, NOW(), NOW())
                ON DUPLICATE KEY UPDATE `updated_at` = NOW()");
            $stmt->execute();

            // 2. Default System Settings
            $settings = [
                ['key' => 'site_name', 'value' => $appConfig['site_name'] ?? 'ApexPulse', 'type' => 'string', 'description' => 'Brand title'],
                ['key' => 'site_description', 'value' => 'Premium Next-Generation SMM Growth Platform', 'type' => 'string', 'description' => 'Meta description'],
                ['key' => 'contact_email', 'value' => $adminData['email'] ?? 'support@apexpulse.io', 'type' => 'string', 'description' => 'Official support contact'],
                ['key' => 'default_currency', 'value' => $appConfig['default_currency'] ?? 'INR', 'type' => 'string', 'description' => 'Default currency code'],
                ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'string', 'description' => 'Maintenance switch'],
                ['key' => 'maintenance_message', 'value' => 'We are undergoing scheduled upgrades. We will return online shortly.', 'type' => 'string', 'description' => 'Maintenance message'],
                ['key' => 'admin_slug', 'value' => $appConfig['admin_slug'] ?? 'admin', 'type' => 'string', 'description' => 'Admin route path'],
            ];

            $setStmt = $pdo->prepare("INSERT INTO `site_settings` (`key`, `value`, `type`, `description`, `updated_at`) 
                VALUES (:key, :value, :type, :description, NOW()) 
                ON DUPLICATE KEY UPDATE `value` = :value, `updated_at` = NOW()");

            foreach ($settings as $s) {
                $setStmt->execute($s);
            }

            // 3. Default Categories
            $catStmt = $pdo->prepare("INSERT INTO `categories` (`name`, `icon`, `sort_order`, `status`, `created_at`, `updated_at`)
                VALUES 
                ('Instagram Growth & Services', 'camera', 1, 'active', NOW(), NOW()),
                ('YouTube Views & Watchtime', 'video', 2, 'active', NOW(), NOW()),
                ('Telegram Channel Members', 'send', 3, 'active', NOW(), NOW()),
                ('Twitter / X Engagement', 'at-sign', 4, 'active', NOW(), NOW())
                ON DUPLICATE KEY UPDATE `updated_at` = NOW()");
            $catStmt->execute();

            // 4. Default Payment Gateways (Ready for configuration, disabled by default)
            $gwStmt = $pdo->prepare("INSERT INTO `payment_gateways` (`code`, `name`, `min_amount`, `max_amount`, `fee_percentage`, `currency_code`, `credentials`, `instructions`, `is_active`, `created_at`, `updated_at`)
                VALUES 
                ('razorpay', 'Razorpay UPI / Cards / NetBanking', 100.0000, 500000.0000, 2.00, 'INR', '', 'Scan the UPI QR code or pay via Indian debit/credit cards.', 0, NOW(), NOW()),
                ('crypto', 'Cryptocurrency (USDT / BTC)', 500.0000, 1000000.0000, 1.00, 'USD', '', 'Send USDT TRC20 to merchant wallet. Instant credit upon 1 confirmation.', 0, NOW(), NOW())
                ON DUPLICATE KEY UPDATE `updated_at` = NOW()");
            $gwStmt->execute();

            // 5. Create First Administrator Account (Hashed with PASSWORD_DEFAULT)
            $passwordHash = password_hash($adminData['password'], PASSWORD_DEFAULT);
            $adminEmail = strtolower(trim($adminData['email']));
            $adminName = trim($adminData['name']);

            // Check if admin already exists
            $userCheck = $pdo->prepare("SELECT `id` FROM `users` WHERE `email` = :email");
            $userCheck->execute([':email' => $adminEmail]);
            $existingAdmin = $userCheck->fetch();

            if ($existingAdmin) {
                $adminId = $existingAdmin['id'];
                $upAdmin = $pdo->prepare("UPDATE `users` SET `name` = :name, `password_hash` = :hash, `role` = 'admin', `status` = 'active', `updated_at` = NOW() WHERE `id` = :id");
                $upAdmin->execute([':name' => $adminName, ':hash' => $passwordHash, ':id' => $adminId]);
            } else {
                $insAdmin = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `status`, `currency_preference`, `created_at`, `updated_at`)
                    VALUES (:name, :email, :hash, 'admin', 'active', 'INR', NOW(), NOW())");
                $insAdmin->execute([':name' => $adminName, ':email' => $adminEmail, ':hash' => $passwordHash]);
                $adminId = $pdo->lastInsertId();
            }

            // Provision admin wallet
            $wCheck = $pdo->prepare("SELECT `id` FROM `wallets` WHERE `user_id` = :uid");
            $wCheck->execute([':uid' => $adminId]);
            if (!$wCheck->fetch()) {
                $wIns = $pdo->prepare("INSERT INTO `wallets` (`user_id`, `balance`, `created_at`, `updated_at`) VALUES (:uid, '0.0000', NOW(), NOW())");
                $wIns->execute([':uid' => $adminId]);
            }

            // Write .env configuration file
            $this->saveEnvironmentConfig($dbConfig, $appConfig);

            // Create Installation Lock File
            $this->createLockFile([
                'admin_email' => $adminEmail,
                'db_name'     => $dbName,
                'db_host'     => $host,
                'site_name'   => $appConfig['site_name'],
            ]);

            return [
                'success'  => true,
                'admin_id' => $adminId,
                'message'  => 'Database installed and administrator provisioned successfully.',
            ];
        } catch (Exception $e) {
            $err = $e->getMessage();
            $err = str_replace($pass, '***', $err);

            return [
                'success' => false,
                'error'   => 'Database Installation Exception: ' . $err,
            ];
        }
    }

    /**
     * DDL SQL Schema for Core Tables in MySQL 8.x
     */
    protected function getCoreDatabaseSchema(): string
    {
        return <<<SQL
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `status` ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
  `currency_preference` VARCHAR(10) NOT NULL DEFAULT 'INR',
  `reset_token` VARCHAR(100) NULL,
  `reset_expires_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL,
  `code` VARCHAR(10) NOT NULL UNIQUE,
  `symbol` VARCHAR(10) NOT NULL,
  `rate_to_inr` DECIMAL(18,6) NOT NULL DEFAULT '1.000000',
  `decimal_precision` TINYINT NOT NULL DEFAULT 2,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `wallets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
  `balance` DECIMAL(16,4) NOT NULL DEFAULT '0.0000',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_wallets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `wallet_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `wallet_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('deposit','order_debit','refund','manual_adjustment') NOT NULL,
  `amount` DECIMAL(16,4) NOT NULL,
  `opening_balance` DECIMAL(16,4) NOT NULL,
  `closing_balance` DECIMAL(16,4) NOT NULL,
  `currency_code` VARCHAR(10) NOT NULL DEFAULT 'INR',
  `reference_id` VARCHAR(100) NULL,
  `remarks` TEXT NULL,
  `admin_id` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `wallet_id` (`wallet_id`),
  KEY `type` (`type`),
  CONSTRAINT `fk_wt_wallet` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `icon` VARCHAR(100) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `providers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `api_url` VARCHAR(255) NOT NULL,
  `api_key` TEXT NOT NULL,
  `balance` DECIMAL(16,4) NOT NULL DEFAULT '0.0000',
  `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `last_sync_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `provider_services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider_id` INT UNSIGNED NOT NULL,
  `remote_service_id` VARCHAR(100) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(60) NOT NULL DEFAULT 'Default',
  `category` VARCHAR(255) NULL,
  `rate` DECIMAL(16,4) NOT NULL DEFAULT '0.0000',
  `min` INT NOT NULL DEFAULT 10,
  `max` INT NOT NULL DEFAULT 100000,
  `dripfeed` TINYINT NOT NULL DEFAULT 0,
  `refill` TINYINT NOT NULL DEFAULT 0,
  `cancel` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  KEY `remote_service_id` (`remote_service_id`),
  CONSTRAINT `fk_ps_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `provider_id` INT UNSIGNED NULL,
  `provider_service_id` VARCHAR(100) NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `min_quantity` INT NOT NULL DEFAULT 10,
  `max_quantity` INT NOT NULL DEFAULT 100000,
  `rate_per_1k` DECIMAL(16,4) NOT NULL DEFAULT '0.0000',
  `margin_percentage` DECIMAL(6,2) NOT NULL DEFAULT '15.00',
  `provider_rate` DECIMAL(16,4) NULL,
  `provider_currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `provider_id` (`provider_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_services_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `provider_id` INT UNSIGNED NULL,
  `provider_service_id` VARCHAR(100) NULL,
  `provider_order_id` VARCHAR(100) NULL,
  `link` TEXT NOT NULL,
  `quantity` INT NOT NULL,
  `charge` DECIMAL(16,4) NOT NULL,
  `display_currency` VARCHAR(10) NOT NULL DEFAULT 'INR',
  `display_charge` DECIMAL(16,4) NOT NULL DEFAULT '0.0000',
  `start_counter` INT NOT NULL DEFAULT 0,
  `remains` INT NOT NULL DEFAULT 0,
  `status` ENUM('pending','processing','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `provider_status` VARCHAR(50) NULL,
  `api_response` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`),
  KEY `provider_id` (`provider_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orders_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_gateways` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `min_amount` DECIMAL(16,4) NOT NULL DEFAULT '100.0000',
  `max_amount` DECIMAL(16,4) NOT NULL DEFAULT '500000.0000',
  `fee_percentage` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `currency_code` VARCHAR(10) NOT NULL DEFAULT 'INR',
  `credentials` TEXT NULL,
  `instructions` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `gateway_id` INT UNSIGNED NOT NULL,
  `transaction_reference` VARCHAR(100) NOT NULL UNIQUE,
  `gateway_transaction_id` VARCHAR(150) NULL,
  `amount` DECIMAL(16,4) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'INR',
  `amount_in_inr` DECIMAL(16,4) NOT NULL,
  `fee` DECIMAL(16,4) NOT NULL DEFAULT '0.0000',
  `status` ENUM('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `gateway_payload` TEXT NULL,
  `gateway_response` TEXT NULL,
  `verified_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_pt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pt_gateway` FOREIGN KEY (`gateway_id`) REFERENCES `payment_gateways` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `priority` ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `status` ENUM('open','pending','closed') NOT NULL DEFAULT 'open',
  `last_reply_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ticket_messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` BIGINT UNSIGNED NOT NULL,
  `sender_id` BIGINT UNSIGNED NOT NULL,
  `sender_type` ENUM('user','admin') NOT NULL DEFAULT 'user',
  `message` TEXT NOT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `fk_tm_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'string',
  `description` VARCHAR(255) NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
    }
}
