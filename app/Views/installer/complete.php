<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    copiedIndex: null,
    copyToClipboard(text, idx) {
        navigator.clipboard.writeText(text);
        this.copiedIndex = idx;
        setTimeout(() => { this.copiedIndex = null; }, 2500);
    }
}">

    <!-- Success Header -->
    <div class="text-center space-y-2 pb-2">
        <div class="w-14 h-14 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 mx-auto flex items-center justify-center shadow-[0_0_25px_rgba(16,185,129,0.3)]">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight">Installation Successfully Completed!</h1>
        <p class="text-xs text-slate-400 max-w-md mx-auto">
            ApexPulse SMM Panel is now configured, secured with production encryption keys, and ready for operations.
        </p>
    </div>

    <!-- Summary Details (No Passwords Exposed) -->
    <div class="rounded-xl bg-[#050914] border border-slate-800 p-4 space-y-3">
        <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Installation Configuration Summary</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80">
                <span class="text-slate-500 text-[10px] uppercase font-mono block">Site URL</span>
                <span class="font-mono text-white text-xs truncate block mt-0.5"><?= esc($site_url ?? site_url()) ?></span>
            </div>
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80">
                <span class="text-slate-500 text-[10px] uppercase font-mono block">Admin Panel Gateway</span>
                <span class="font-mono text-blue-400 text-xs truncate block mt-0.5"><?= esc($admin_url ?? site_url('admin/login')) ?></span>
            </div>
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80">
                <span class="text-slate-500 text-[10px] uppercase font-mono block">Administrator Account</span>
                <span class="font-mono text-white text-xs truncate block mt-0.5"><?= esc($admin_email ?? 'admin@apexpulse.io') ?></span>
            </div>
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80">
                <span class="text-slate-500 text-[10px] uppercase font-mono block">Base Currency &amp; Precision</span>
                <span class="font-mono text-white text-xs block mt-0.5">INR (₹) &bull; DECIMAL(16,4)</span>
            </div>
        </div>
    </div>

    <!-- Navigation Action Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
        <a href="<?= esc($admin_url ?? site_url('admin/login')) ?>" class="py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs text-center transition-all shadow-[0_0_20px_rgba(37,99,235,0.4)] flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Go to Admin Login</span>
        </a>

        <a href="<?= esc($site_url ?? site_url()) ?>" class="py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs text-center transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span>Go to Website Homepage</span>
        </a>
    </div>

    <!-- Security Reminder -->
    <div class="p-4 rounded-xl bg-amber-950/30 border border-amber-500/30 text-xs text-amber-200 space-y-1.5">
        <div class="flex items-center gap-2 font-bold text-amber-400">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Security Warning &amp; Lockdown</span>
        </div>
        <p class="leading-relaxed text-[11px] text-amber-300/90">
            The security lock file <code class="bg-black/40 px-1 py-0.5 rounded font-mono text-amber-300">writable/installed.lock</code> has been created. The installer is now permanently locked against replay or unauthorized overwrites. For added production security, you may optionally remove or restrict the <code class="font-mono text-amber-300">app/Controllers/Installer.php</code> file.
        </p>
    </div>

    <!-- Cron Job Configuration Instructions -->
    <div class="rounded-xl bg-[#050914] border border-slate-800 p-4 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Automated Background Cron Jobs</h3>
            <span class="text-[10px] font-mono text-blue-400">cPanel / Linux Crontab</span>
        </div>

        <p class="text-[11px] text-slate-400 leading-relaxed">
            To automate SMM provider order forwarding, status syncing, and live currency exchange updates, add these cron job tasks to your server's crontab (e.g. via <code class="font-mono text-slate-300">crontab -e</code> or cPanel Cron Jobs):
        </p>

        <div class="space-y-2 text-xs font-mono">
            
            <!-- Cron 1: Order Dispatch -->
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80 flex items-center justify-between gap-3">
                <div class="truncate">
                    <span class="text-[10px] text-slate-500 uppercase block font-sans">1. Order Dispatch to Providers (Every 2 minutes)</span>
                    <span class="text-blue-400 text-[11px] truncate block select-all">* /2 * * * * cd <?= ROOTPATH ?> && php spark orders:dispatch >> /dev/null 2>&1</span>
                </div>
                <button type="button" @click="copyToClipboard('* /2 * * * * cd <?= ROOTPATH ?> && php spark orders:dispatch >> /dev/null 2>&1', 1)" class="shrink-0 px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 transition-colors">
                    <span x-text="copiedIndex === 1 ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>

            <!-- Cron 2: Order Status Sync -->
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80 flex items-center justify-between gap-3">
                <div class="truncate">
                    <span class="text-[10px] text-slate-500 uppercase block font-sans">2. Provider Order Status Sync (Every 5 minutes)</span>
                    <span class="text-blue-400 text-[11px] truncate block select-all">*/5 * * * * cd <?= ROOTPATH ?> && php spark orders:sync >> /dev/null 2>&1</span>
                </div>
                <button type="button" @click="copyToClipboard('*/5 * * * * cd <?= ROOTPATH ?> && php spark orders:sync >> /dev/null 2>&1', 2)" class="shrink-0 px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 transition-colors">
                    <span x-text="copiedIndex === 2 ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>

            <!-- Cron 3: Provider Balance Check -->
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80 flex items-center justify-between gap-3">
                <div class="truncate">
                    <span class="text-[10px] text-slate-500 uppercase block font-sans">3. Provider Balance Verification (Hourly)</span>
                    <span class="text-blue-400 text-[11px] truncate block select-all">0 * * * * cd <?= ROOTPATH ?> && php spark providers:balance >> /dev/null 2>&1</span>
                </div>
                <button type="button" @click="copyToClipboard('0 * * * * cd <?= ROOTPATH ?> && php spark providers:balance >> /dev/null 2>&1', 3)" class="shrink-0 px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 transition-colors">
                    <span x-text="copiedIndex === 3 ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>

            <!-- Cron 4: Currency Rate Sync -->
            <div class="p-3 rounded-lg bg-[#070C1A] border border-slate-800/80 flex items-center justify-between gap-3">
                <div class="truncate">
                    <span class="text-[10px] text-slate-500 uppercase block font-sans">4. Currency Exchange Rate Sync (Every 12 Hours)</span>
                    <span class="text-blue-400 text-[11px] truncate block select-all">0 */12 * * * cd <?= ROOTPATH ?> && php spark currencies:sync >> /dev/null 2>&1</span>
                </div>
                <button type="button" @click="copyToClipboard('0 */12 * * * cd <?= ROOTPATH ?> && php spark currencies:sync >> /dev/null 2>&1', 4)" class="shrink-0 px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 transition-colors">
                    <span x-text="copiedIndex === 4 ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>

        </div>
    </div>

</div>
<?= $this->endSection() ?>
