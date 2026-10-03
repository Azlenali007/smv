<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    host: '<?= esc($dbConfig['host'] ?? '127.0.0.1') ?>',
    port: '<?= esc($dbConfig['port'] ?? 3306) ?>',
    database: '<?= esc($dbConfig['database'] ?? 'smm_panel') ?>',
    username: '<?= esc($dbConfig['username'] ?? 'root') ?>',
    password: '<?= esc($dbConfig['password'] ?? '') ?>',
    testing: false,
    testResult: null,
    testSuccess: false,
    showPass: false,

    async testConnection() {
        if (!this.database || !this.username) {
            this.testResult = 'Database name and username are required before testing.';
            this.testSuccess = false;
            return;
        }

        this.testing = true;
        this.testResult = null;

        try {
            const formData = new FormData();
            formData.append('host', this.host);
            formData.append('port', this.port);
            formData.append('database', this.database);
            formData.append('username', this.username);
            formData.append('password', this.password);

            const res = await fetch('<?= site_url('install/test-db') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            this.testing = false;
            if (data.success) {
                this.testSuccess = true;
                this.testResult = data.message || 'Database connection verified successfully!';
            } else {
                this.testSuccess = false;
                this.testResult = data.error || 'Failed to connect to MySQL server.';
            }
        } catch (err) {
            this.testing = false;
            this.testSuccess = false;
            this.testResult = 'Network error while attempting database connection test.';
        }
    }
}">

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Database Configuration</h2>
        <p class="text-xs text-slate-400 mt-1">Specify your MySQL 8.x database server credentials and test connectivity.</p>
    </div>

    <form action="<?= site_url('install/database') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Database Host</label>
                <input type="text" name="host" x-model="host" required placeholder="127.0.0.1 or localhost" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Port</label>
                <input type="number" name="port" x-model="port" required placeholder="3306" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Database Name</label>
            <input type="text" name="database" x-model="database" required placeholder="smm_panel" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            <p class="text-[10px] text-slate-500 mt-1">If the database does not exist, the installer will attempt to create it if user permissions permit.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Username</label>
                <input type="text" name="username" x-model="username" required placeholder="smm_user" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" x-model="password" placeholder="••••••••" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono pr-12">
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs">
                        <span x-text="showPass ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Connection Test Result Box -->
        <div x-show="testResult !== null" x-cloak class="p-3.5 rounded-xl border text-xs leading-relaxed"
             :class="testSuccess ? 'bg-emerald-950/40 border-emerald-500/30 text-emerald-300' : 'bg-rose-950/40 border-rose-500/30 text-rose-300'">
            <div class="flex items-center gap-2 font-bold mb-1">
                <span x-text="testSuccess ? '✓ Connection Verified' : '✗ Connection Failed'"></span>
            </div>
            <p x-text="testResult"></p>
        </div>

        <!-- Action Buttons -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-800/80">
            <a href="<?= site_url('install/requirements') ?>" class="text-xs text-slate-400 hover:text-white transition-colors">
                &larr; Back to Requirements
            </a>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" @click="testConnection()" :disabled="testing" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs transition-colors disabled:opacity-50">
                    <span x-show="!testing">Test Connection</span>
                    <span x-show="testing" x-cloak>Testing PDO MySQL...</span>
                </button>

                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)] flex items-center justify-center gap-2">
                    <span>Save &amp; Continue</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </div>
    </form>

</div>
<?= $this->endSection() ?>
