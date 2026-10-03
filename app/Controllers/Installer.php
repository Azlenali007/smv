<?php

namespace App\Controllers;

use App\Services\InstallerService;
use CodeIgniter\Controller;

class Installer extends Controller
{
    protected InstallerService $installerService;
    protected $helpers = ['url', 'form'];

    public function __construct()
    {
        $this->installerService = new InstallerService();
    }

    /**
     * Guard: if already installed, block access immediately
     */
    protected function checkLock()
    {
        if ($this->installerService->isInstalled()) {
            return redirect()->to(site_url('install/already-installed'));
        }
        return null;
    }

    /**
     * Step 1: Welcome
     */
    public function index()
    {
        if ($lock = $this->checkLock()) return $lock;

        return view('installer/welcome', [
            'step'  => 1,
            'title' => 'Welcome to ApexPulse Installation',
        ]);
    }

    /**
     * Step 2: Requirements Check
     */
    public function requirements()
    {
        if ($lock = $this->checkLock()) return $lock;

        $checkData = $this->installerService->checkRequirements();

        return view('installer/requirements', [
            'step'       => 2,
            'title'      => 'System Requirements Check',
            'checks'     => $checkData['checks'],
            'all_passed' => $checkData['all_passed'],
        ]);
    }

    /**
     * Step 3: Database Configuration Form
     */
    public function database()
    {
        if ($lock = $this->checkLock()) return $lock;

        // Verify requirements were passed
        $reqs = $this->installerService->checkRequirements();
        if (!$reqs['all_passed']) {
            return redirect()->to(site_url('install/requirements'))->with('error', 'Please resolve all required environment checks before configuring the database.');
        }

        $session = session();
        $dbConfig = $session->get('install_db_config') ?? [
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'database' => 'smm_panel',
            'username' => 'root',
            'password' => '',
        ];

        return view('installer/database', [
            'step'     => 3,
            'title'    => 'Database Configuration',
            'dbConfig' => $dbConfig,
        ]);
    }

    /**
     * Step 3 AJAX: Real Database Connection Test
     */
    public function testDatabase()
    {
        if ($this->installerService->isInstalled()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Application is already locked and installed.']);
        }

        $host     = trim((string)$this->request->getPost('host')) ?: '127.0.0.1';
        $port     = (int)$this->request->getPost('port') ?: 3306;
        $database = trim((string)$this->request->getPost('database'));
        $username = trim((string)$this->request->getPost('username'));
        $password = (string)$this->request->getPost('password');

        if (empty($database) || empty($username)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Database name and username are required.']);
        }

        $result = $this->installerService->testDatabaseConnection($host, $port, $database, $username, $password);
        return $this->response->setJSON($result);
    }

    /**
     * Step 3 POST: Save Database Configuration in session
     */
    public function saveDatabase()
    {
        if ($lock = $this->checkLock()) return $lock;

        $rules = [
            'host'     => 'required',
            'port'     => 'required|integer',
            'database' => 'required|min_length[2]',
            'username' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $host     = trim((string)$this->request->getPost('host'));
        $port     = (int)$this->request->getPost('port');
        $database = trim((string)$this->request->getPost('database'));
        $username = trim((string)$this->request->getPost('username'));
        $password = (string)$this->request->getPost('password');

        // Test connection before proceeding
        $test = $this->installerService->testDatabaseConnection($host, $port, $database, $username, $password);
        if (!$test['success']) {
            return redirect()->back()->withInput()->with('error', $test['error']);
        }

        session()->set('install_db_config', [
            'host'     => $host,
            'port'     => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ]);

        return redirect()->to(site_url('install/configuration'));
    }

    /**
     * Step 4: Application Configuration Form
     */
    public function configuration()
    {
        if ($lock = $this->checkLock()) return $lock;

        if (!session()->get('install_db_config')) {
            return redirect()->to(site_url('install/database'))->with('error', 'Please configure your database connection first.');
        }

        $session = session();
        $detectedUrl = rtrim(site_url(), '/') . '/';
        $appConfig = $session->get('install_app_config') ?? [
            'site_name'        => 'ApexPulse SMM',
            'site_url'         => $detectedUrl,
            'admin_slug'       => 'admin',
            'default_currency' => 'INR',
            'timezone'         => 'UTC',
            'app_env'          => 'production',
        ];

        return view('installer/configuration', [
            'step'      => 4,
            'title'     => 'Application Configuration',
            'appConfig' => $appConfig,
        ]);
    }

    /**
     * Step 4 POST: Save Application Configuration in session
     */
    public function saveConfiguration()
    {
        if ($lock = $this->checkLock()) return $lock;

        $rules = [
            'site_name'        => 'required|min_length[2]|max_length[100]',
            'site_url'         => 'required|valid_url',
            'default_currency' => 'required',
            'timezone'         => 'required',
            'app_env'          => 'in_list[production,development]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        session()->set('install_app_config', [
            'site_name'        => trim((string)$this->request->getPost('site_name')),
            'site_url'         => trim((string)$this->request->getPost('site_url')),
            'admin_slug'       => trim((string)$this->request->getPost('admin_slug')) ?: 'admin',
            'default_currency' => strtoupper(trim((string)$this->request->getPost('default_currency'))),
            'timezone'         => trim((string)$this->request->getPost('timezone')),
            'app_env'          => $this->request->getPost('app_env'),
        ]);

        return redirect()->to(site_url('install/admin'));
    }

    /**
     * Step 5: Administrator Account Form
     */
    public function admin()
    {
        if ($lock = $this->checkLock()) return $lock;

        if (!session()->get('install_db_config') || !session()->get('install_app_config')) {
            return redirect()->to(site_url('install/database'))->with('error', 'Incomplete previous installation steps.');
        }

        return view('installer/admin', [
            'step'  => 5,
            'title' => 'Create Administrator Account',
        ]);
    }

    /**
     * Step 5 POST: Save Administrator credentials in session & advance to Step 6
     */
    public function saveAdmin()
    {
        if ($lock = $this->checkLock()) return $lock;

        $rules = [
            'name'                  => 'required|min_length[2]|max_length[100]',
            'email'                 => 'required|valid_email',
            'password'              => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        session()->set('install_admin_data', [
            'name'     => trim((string)$this->request->getPost('name')),
            'email'    => strtolower(trim((string)$this->request->getPost('email'))),
            'password' => (string)$this->request->getPost('password'),
        ]);

        return redirect()->to(site_url('install/installation'));
    }

    /**
     * Step 6 & 7: Stepped Installation & Finalization Screen
     */
    public function installation()
    {
        if ($lock = $this->checkLock()) return $lock;

        $session = session();
        if (!$session->get('install_db_config') || !$session->get('install_app_config') || !$session->get('install_admin_data')) {
            return redirect()->to(site_url('install/admin'))->with('error', 'Please configure the administrator account first.');
        }

        return view('installer/installation', [
            'step'  => 6,
            'title' => 'Database Installation & Finalization',
        ]);
    }

    /**
     * Step 6 & 7 AJAX: Execute MySQL migrations, seeding, .env generation, and lockfile
     */
    public function runInstall()
    {
        if ($this->installerService->isInstalled()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Application is already installed and locked.']);
        }

        $session   = session();
        $dbConfig  = $session->get('install_db_config');
        $appConfig = $session->get('install_app_config');
        $adminData = $session->get('install_admin_data');

        if (!$dbConfig || !$appConfig || !$adminData) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Session data expired or missing. Please return to Step 3 and re-enter database configuration.',
            ]);
        }

        // Execute real installation
        $installResult = $this->installerService->installDatabaseAndAdmin($dbConfig, $appConfig, $adminData);

        if (!$installResult['success']) {
            return $this->response->setJSON($installResult);
        }

        // Store installation completion info in flashdata
        session()->setFlashdata('installed_site_url', $appConfig['site_url']);
        session()->setFlashdata('installed_admin_url', rtrim($appConfig['site_url'], '/') . '/' . ($appConfig['admin_slug'] ?? 'admin') . '/login');
        session()->setFlashdata('installed_admin_email', $adminData['email']);

        // Clear temporary install session variables
        session()->remove(['install_db_config', 'install_app_config', 'install_admin_data']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Database tables created, baseline records seeded, and environment finalized successfully.',
        ]);
    }

    /**
     * Step 6 & 7 POST Fallback (Non-AJAX direct execution)
     */
    public function process()
    {
        if ($lock = $this->checkLock()) return $lock;

        $rules = [
            'name'                  => 'required|min_length[2]|max_length[100]',
            'email'                 => 'required|valid_email',
            'password'              => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $session   = session();
        $dbConfig  = $session->get('install_db_config');
        $appConfig = $session->get('install_app_config');

        if (!$dbConfig || !$appConfig) {
            return redirect()->to(site_url('install/database'))->with('error', 'Session expired. Please re-enter database configuration.');
        }

        $adminData = [
            'name'     => trim((string)$this->request->getPost('name')),
            'email'    => strtolower(trim((string)$this->request->getPost('email'))),
            'password' => (string)$this->request->getPost('password'),
        ];

        // Execute real installation
        $installResult = $this->installerService->installDatabaseAndAdmin($dbConfig, $appConfig, $adminData);

        if (!$installResult['success']) {
            return redirect()->back()->withInput()->with('error', $installResult['error']);
        }

        // Store installation completion info in flashdata
        session()->setFlashdata('installed_site_url', $appConfig['site_url']);
        session()->setFlashdata('installed_admin_url', rtrim($appConfig['site_url'], '/') . '/' . ($appConfig['admin_slug'] ?? 'admin') . '/login');
        session()->setFlashdata('installed_admin_email', $adminData['email']);

        // Clear temporary install session variables
        session()->remove(['install_db_config', 'install_app_config', 'install_admin_data']);

        return redirect()->to(site_url('install/complete'));
    }

    /**
     * Step 8: Installation Complete
     */
    public function complete()
    {
        return view('installer/complete', [
            'step'       => 8,
            'title'      => 'Installation Successfully Completed',
            'site_url'   => session()->getFlashdata('installed_site_url') ?: site_url(),
            'admin_url'  => session()->getFlashdata('installed_admin_url') ?: site_url('admin/login'),
            'admin_email'=> session()->getFlashdata('installed_admin_email') ?: 'Administrator',
        ]);
    }

    /**
     * Already Installed Protection View
     */
    public function alreadyInstalled()
    {
        return view('installer/already_installed', [
            'title' => 'Application Already Installed',
        ]);
    }
}
