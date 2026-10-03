<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="space-y-3">
        <h1 class="text-2xl font-bold text-white tracking-tight">Platform Installation Wizard</h1>
        <p class="text-xs text-slate-300 leading-relaxed">
            Welcome to the automated web-based setup for <strong class="text-blue-400">ApexPulse SMM</strong>. This wizard will guide you through inspecting server requirements, verifying database connectivity, configuring application parameters, and provisioning the primary administrator account.
        </p>
    </div>

    <!-- Prerequisites Overview -->
    <div class="p-4 rounded-xl bg-[#050914] border border-slate-800 space-y-3 text-xs">
        <h3 class="font-bold text-slate-200 uppercase tracking-wider text-[11px]">Installation Overview</h3>
        <ul class="space-y-2 text-slate-400">
            <li class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                <span>Requires PHP 8.2 or newer with PDO MySQL, cURL, OpenSSL, and Mbstring.</span>
            </li>
            <li class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                <span>Requires MySQL 8.x host credentials with schema creation privileges.</span>
            </li>
            <li class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                <span>Automated migration of core tables without mock or fake records.</span>
            </li>
            <li class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                <span>Permanent <code class="font-mono text-slate-300">install.lock</code> protection to prevent unauthorized overrides.</span>
            </li>
        </ul>
    </div>

    <div class="pt-2 flex items-center justify-between">
        <a href="<?= site_url('/') ?>" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">
            &larr; Exit to Homepage
        </a>
        <a href="<?= site_url('install/requirements') ?>" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)] flex items-center gap-2">
            <span>Start Installation</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>

</div>
<?= $this->endSection() ?>
