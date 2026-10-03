<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// ==========================================
// 1. PUBLIC ROUTES (with Maintenance check)
// ==========================================
$routes->group('', ['filter' => 'maintenance'], static function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('services', 'Home::services');
    $routes->get('faq', 'Home::faq');
    $routes->get('terms', 'Home::terms');
});

// ==========================================
// 2. AUTHENTICATION ROUTES (Guest Filter)
// ==========================================
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::attemptRegister');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('forgot-password', 'Auth::attemptForgotPassword');
    $routes->get('reset-password/(:segment)', 'Auth::resetPassword/$1');
    $routes->post('reset-password', 'Auth::attemptResetPassword');
});

$routes->get('logout', 'Auth::logout');

// ==========================================
// 3. USER PANEL ROUTES (Protected by AuthFilter)
// ==========================================
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');

    // New Order
    $routes->get('new-order', 'User\Order::newOrder');
    $routes->post('order/calculate', 'User\Order::calculatePrice');
    $routes->post('order/store', 'User\Order::placeOrder');

    // Orders Management
    $routes->get('orders', 'User\Order::index');
    $routes->get('orders/(:num)', 'User\Order::show/$1');

    // Services Catalog (authenticated view)
    $routes->get('user-services', 'User\Service::index');
    $routes->get('api/services-by-category/(:num)', 'User\Service::byCategory/$1');

    // Wallet & Add Funds
    $routes->get('add-funds', 'User\Wallet::index');
    $routes->post('wallet/deposit', 'User\Wallet::initiateDeposit');
    $routes->get('wallet/transactions', 'User\Wallet::transactions');

    // Support Tickets
    $routes->get('tickets', 'User\Ticket::index');
    $routes->get('tickets/create', 'User\Ticket::create');
    $routes->post('tickets/store', 'User\Ticket::store');
    $routes->get('tickets/(:num)', 'User\Ticket::show/$1');
    $routes->post('tickets/(:num)/reply', 'User\Ticket::reply/$1');

    // User Profile & Currency Preference
    $routes->get('profile', 'User\Profile::index');
    $routes->post('profile/update', 'User\Profile::updateProfile');
    $routes->post('profile/change-password', 'User\Profile::changePassword');
    $routes->post('profile/set-currency', 'User\Profile::setCurrency');
});

// ==========================================
// 4. ADMIN PANEL ROUTES (Protected by AdminFilter)
// ==========================================
$routes->group('admin', static function ($routes) {
    // Admin Auth (Guest Admin)
    $routes->get('login', 'Admin\Auth::login');
    $routes->post('login', 'Admin\Auth::attemptLogin');
    $routes->get('logout', 'Admin\Auth::logout');

    // Protected Admin Console
    $routes->group('', ['filter' => 'admin_auth'], static function ($routes) {
        $routes->get('', 'Admin\Dashboard::index');
        $routes->get('dashboard', 'Admin\Dashboard::index');

        // User Management
        $routes->get('users', 'Admin\User::index');
        $routes->get('users/(:num)', 'Admin\User::show/$1');
        $routes->post('users/(:num)/toggle-status', 'Admin\User::toggleStatus/$1');
        $routes->post('users/(:num)/adjust-wallet', 'Admin\User::adjustWallet/$1');

        // Categories
        $routes->get('categories', 'Admin\Category::index');
        $routes->post('categories/store', 'Admin\Category::store');
        $routes->post('categories/(:num)/update', 'Admin\Category::update/$1');
        $routes->post('categories/(:num)/delete', 'Admin\Category::delete/$1');

        // Services
        $routes->get('services', 'Admin\Service::index');
        $routes->get('services/create', 'Admin\Service::create');
        $routes->post('services/store', 'Admin\Service::store');
        $routes->get('services/(:num)/edit', 'Admin\Service::edit/$1');
        $routes->post('services/(:num)/update', 'Admin\Service::update/$1');
        $routes->post('services/(:num)/delete', 'Admin\Service::delete/$1');
        $routes->post('services/(:num)/toggle', 'Admin\Service::toggle/$1');

        // Providers & API Synchronization
        $routes->get('providers', 'Admin\Provider::index');
        $routes->post('providers/store', 'Admin\Provider::store');
        $routes->post('providers/(:num)/update', 'Admin\Provider::update/$1');
        $routes->post('providers/(:num)/delete', 'Admin\Provider::delete/$1');
        $routes->post('providers/(:num)/test', 'Admin\Provider::testConnection/$1');
        $routes->get('providers/(:num)/services', 'Admin\Provider::fetchServices/$1');
        $routes->post('providers/(:num)/import', 'Admin\Provider::importServices/$1');

        // Orders Management
        $routes->get('orders', 'Admin\Order::index');
        $routes->get('orders/(:num)', 'Admin\Order::show/$1');
        $routes->post('orders/(:num)/status', 'Admin\Order::updateStatus/$1');
        $routes->post('orders/(:num)/resend', 'Admin\Order::resendToProvider/$1');

        // Payment Gateways & Transactions
        $routes->get('payments', 'Admin\Payment::index');
        $routes->post('payments/gateway/(:num)/update', 'Admin\Payment::updateGateway/$1');
        $routes->get('payments/transactions', 'Admin\Payment::transactions');

        // Multi-Currency Management
        $routes->get('currencies', 'Admin\Currency::index');
        $routes->post('currencies/store', 'Admin\Currency::store');
        $routes->post('currencies/(:num)/update', 'Admin\Currency::update/$1');
        $routes->post('currencies/(:num)/set-default', 'Admin\Currency::setDefault/$1');

        // Support Tickets
        $routes->get('tickets', 'Admin\Ticket::index');
        $routes->get('tickets/(:num)', 'Admin\Ticket::show/$1');
        $routes->post('tickets/(:num)/reply', 'Admin\Ticket::reply/$1');
        $routes->post('tickets/(:num)/status', 'Admin\Ticket::updateStatus/$1');

        // Site Settings & Maintenance
        $routes->get('settings', 'Admin\Setting::index');
        $routes->post('settings/update', 'Admin\Setting::update');
    });
});

// ==========================================
// 5. WEB INSTALLER WIZARD ROUTES
// ==========================================
$routes->get('install', 'Installer::index');
$routes->group('install', static function ($routes) {
    $routes->get('/', 'Installer::index');
    $routes->get('', 'Installer::index');
    $routes->get('requirements', 'Installer::requirements');
    $routes->get('database', 'Installer::database');
    $routes->post('database', 'Installer::saveDatabase');
    $routes->post('test-db', 'Installer::testDatabase');
    $routes->get('configuration', 'Installer::configuration');
    $routes->post('configuration', 'Installer::saveConfiguration');
    $routes->get('admin', 'Installer::admin');
    $routes->post('admin', 'Installer::saveAdmin');
    $routes->get('install', 'Installer::installation');
    $routes->get('installation', 'Installer::installation');
    $routes->post('run-install', 'Installer::runInstall');
    $routes->post('process', 'Installer::process');
    $routes->get('complete', 'Installer::complete');
    $routes->get('already-installed', 'Installer::alreadyInstalled');
});
