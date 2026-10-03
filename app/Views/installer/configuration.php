<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Application Configuration</h2>
        <p class="text-xs text-slate-400 mt-1">Configure global platform attributes, routing slug, base currency, and timezones.</p>
    </div>

    <form action="<?= site_url('install/configuration') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Platform Site Name</label>
                <input type="text" name="site_name" value="<?= esc($appConfig['site_name'] ?? 'ApexPulse SMM') ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Site URL (with trailing slash)</label>
                <input type="url" name="site_url" value="<?= esc($appConfig['site_url'] ?? site_url()) ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Admin Route Slug</label>
                <div class="flex items-center">
                    <span class="px-3 py-2.5 bg-[#070B18] border border-r-0 border-slate-800 rounded-l-xl text-xs text-slate-500 font-mono">/</span>
                    <input type="text" name="admin_slug" value="<?= esc($appConfig['admin_slug'] ?? 'admin') ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-r-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Platform Currency</label>
                <select name="default_currency" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    <option value="INR" selected>INR (₹) - Base Internal Accounting Currency</option>
                    <option value="USD">USD ($) - US Dollar</option>
                    <option value="EUR">EUR (€) - Euro</option>
                    <option value="GBP">GBP (£) - British Pound</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Server Timezone</label>
                <select name="timezone" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    <option value="UTC" selected>UTC (Coordinated Universal Time)</option>
                    <option value="Asia/Kolkata">Asia/Kolkata (IST +05:30)</option>
                    <option value="America/New_York">America/New_York (EST/EDT)</option>
                    <option value="Europe/London">Europe/London (GMT/BST)</option>
                    <option value="Europe/Paris">Europe/Paris (CET)</option>
                    <option value="Asia/Dubai">Asia/Dubai (GST +04:00)</option>
                    <option value="Asia/Singapore">Asia/Singapore (SGT +08:00)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Environment</label>
                <select name="app_env" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="production" selected>Production (Secure, logs to file)</option>
                    <option value="development">Development (Debug output enabled)</option>
                </select>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between border-t border-slate-800/80">
            <a href="<?= site_url('install/database') ?>" class="text-xs text-slate-400 hover:text-white transition-colors">
                &larr; Back to Database
            </a>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)] flex items-center gap-2">
                <span>Continue to Admin Setup</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>

</div>
<?= $this->endSection() ?>
