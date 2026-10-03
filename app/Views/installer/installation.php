<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    status: 'ready', // 'ready', 'running', 'completed', 'error'
    currentSubStep: 0,
    errorMessage: '',
    progressPercent: 0,
    steps: [
        { id: 1, title: 'Database Tables & Schema Migration', desc: '14 Core MySQL tables with indexes & foreign keys', done: false, active: false, error: false },
        { id: 2, title: 'Seed Base Currencies & System Settings', desc: 'INR (₹) 1.000000 base currency, USD, EUR, GBP', done: false, active: false, error: false },
        { id: 3, title: 'Seed Payment Gateways & Categories', desc: 'Razorpay UPI (INR), Crypto gateways & social channels', done: false, active: false, error: false },
        { id: 4, title: 'Provision Administrator & Wallet', desc: 'Securely hashed credentials and initial ledger account', done: false, active: false, error: false },
        { id: 5, title: 'Step 7: Finalize Environment Configuration', desc: 'Generating production .env with encryption key & base URL', done: false, active: false, error: false },
        { id: 6, title: 'Step 7: Create Security Lock File', desc: 'Generating installed.lock to permanently prevent re-installs', done: false, active: false, error: false }
    ],

    async startInstallation() {
        this.status = 'running';
        this.errorMessage = '';

        try {
            // Trigger server-side execution
            this.setStepActive(0);
            this.progressPercent = 15;

            const res = await fetch('<?= site_url('install/run-install') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            });

            const data = await res.json();

            if (!data.success) {
                this.status = 'error';
                this.errorMessage = data.error || 'A database error occurred during installation.';
                if (this.currentSubStep >= 0 && this.currentSubStep < this.steps.length) {
                    this.steps[this.currentSubStep].error = true;
                    this.steps[this.currentSubStep].active = false;
                }
                return;
            }

            // Animate stepped visual progression
            for (let i = 0; i < this.steps.length; i++) {
                this.setStepActive(i);
                this.progressPercent = Math.round(((i + 1) / this.steps.length) * 100);
                await new Promise(r => setTimeout(r, 400));
                this.setStepDone(i);
            }

            this.status = 'completed';
            this.progressPercent = 100;

            // Short pause then redirect to complete
            setTimeout(() => {
                window.location.href = '<?= site_url('install/complete') ?>';
            }, 1200);

        } catch (err) {
            this.status = 'error';
            this.errorMessage = 'Network or server communication failure: ' + (err.message || 'Please check PHP error logs.');
        }
    },

    setStepActive(index) {
        this.currentSubStep = index;
        this.steps.forEach((s, idx) => {
            s.active = (idx === index);
        });
    },

    setStepDone(index) {
        if (this.steps[index]) {
            this.steps[index].done = true;
            this.steps[index].active = false;
        }
    }
}" x-init="setTimeout(() => startInstallation(), 500)">

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Step 6 &amp; 7: Database Installation &amp; Finalization</h2>
        <p class="text-xs text-slate-400 mt-1">Executing MySQL table migrations, seeding default records, and generating security lock files.</p>
    </div>

    <!-- Progress Bar -->
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs font-mono">
            <span class="text-slate-400">Installation Progress:</span>
            <span class="text-blue-400 font-bold" x-text="progressPercent + '%'"></span>
        </div>
        <div class="w-full h-2 rounded-full bg-slate-900 overflow-hidden border border-slate-800">
            <div class="h-full bg-gradient-to-r from-blue-600 via-sky-500 to-emerald-400 transition-all duration-300 rounded-full" :style="'width: ' + progressPercent + '%'"></div>
        </div>
    </div>

    <!-- Live Execution Steps -->
    <div class="rounded-xl bg-[#050914] border border-slate-800 divide-y divide-slate-800/80 overflow-hidden">
        <template x-for="(s, idx) in steps" :key="s.id">
            <div class="p-3.5 flex items-center justify-between transition-colors"
                 :class="s.active ? 'bg-blue-950/20' : (s.done ? 'bg-slate-950/40' : '')">
                <div class="flex items-center gap-3">
                    <!-- Icon Status Indicator -->
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 text-xs font-mono"
                         :class="s.done ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/40' : (s.active ? 'bg-blue-600 text-white animate-pulse' : (s.error ? 'bg-rose-600/20 text-rose-400 border border-rose-500/40' : 'bg-slate-800 text-slate-500'))">
                        <template x-if="s.done"><span>✓</span></template>
                        <template x-if="s.active"><span class="w-2 h-2 rounded-full bg-white"></span></template>
                        <template x-if="s.error"><span>✗</span></template>
                        <template x-if="!s.done && !s.active && !s.error"><span x-text="s.id"></span></template>
                    </div>

                    <div>
                        <div class="text-xs font-semibold" :class="s.done ? 'text-slate-200' : (s.active ? 'text-blue-400 font-bold' : (s.error ? 'text-rose-400' : 'text-slate-400'))" x-text="s.title"></div>
                        <div class="text-[11px] text-slate-500 mt-0.5" x-text="s.desc"></div>
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded"
                          :class="s.done ? 'text-emerald-400 bg-emerald-950/40' : (s.active ? 'text-blue-400 bg-blue-950/60' : (s.error ? 'text-rose-400 bg-rose-950/60' : 'text-slate-600'))"
                          x-text="s.done ? 'Completed' : (s.active ? 'Running...' : (s.error ? 'Failed' : 'Pending'))"></span>
                </div>
            </div>
        </template>
    </div>

    <!-- Error Callout Box -->
    <div x-show="status === 'error'" x-cloak class="p-4 rounded-xl bg-rose-950/50 border border-rose-500/40 text-xs space-y-2">
        <div class="flex items-center gap-2 text-rose-300 font-bold">
            <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Installation Encountered an Error:</span>
        </div>
        <p class="text-rose-200 font-mono text-[11px] break-all leading-relaxed" x-text="errorMessage"></p>
        <div class="pt-2 flex items-center gap-3">
            <button type="button" @click="startInstallation()" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs transition-colors">
                Retry Installation
            </button>
            <a href="<?= site_url('install/database') ?>" class="text-xs text-rose-300 hover:underline">
                Recheck Database Credentials
            </a>
        </div>
    </div>

    <!-- Completion Notification -->
    <div x-show="status === 'completed'" x-cloak class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40 text-xs text-emerald-300 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span class="font-semibold">All database migrations, baseline records, and security lock file created successfully!</span>
        </div>
        <a href="<?= site_url('install/complete') ?>" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold">
            View Final Details &rarr;
        </a>
    </div>

</div>
<?= $this->endSection() ?>
