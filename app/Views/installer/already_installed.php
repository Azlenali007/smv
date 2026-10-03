<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="text-center space-y-6 py-4">

    <div class="w-16 h-16 rounded-2xl bg-amber-600/20 border border-amber-500/30 text-amber-400 mx-auto flex items-center justify-center shadow-[0_0_25px_rgba(245,158,11,0.25)]">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>

    <div class="space-y-2">
        <h1 class="text-2xl font-extrabold text-white tracking-tight">Application Already Installed</h1>
        <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
            ApexPulse SMM Panel is already configured and actively secured. The web installation wizard has been permanently disabled to prevent unauthorized database overrides.
        </p>
    </div>

    <!-- Security Information Box -->
    <div class="p-4 rounded-xl bg-[#050914] border border-slate-800 text-left text-xs space-y-2 font-mono">
        <div class="text-slate-400 font-sans font-bold uppercase text-[11px]">Security Lock State</div>
        <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80 text-amber-300 text-[11px] leading-relaxed">
            Lock File Active: <span class="text-white">writable/installed.lock</span><br>
            If you genuinely need to re-install or reconfigure the database from scratch, delete the lock file manually via SSH or your server file manager.
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
        <a href="<?= site_url('admin/login') ?>" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)] flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span>Proceed to Admin Console</span>
        </a>

        <a href="<?= site_url('/') ?>" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-colors flex items-center justify-center gap-2">
            <span>Return to Homepage</span>
        </a>
    </div>

</div>
<?= $this->endSection() ?>
