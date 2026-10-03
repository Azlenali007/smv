<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">System Requirements &amp; Permissions</h2>
        <p class="text-xs text-slate-400 mt-1">Live inspection of your hosting environment and PHP configuration.</p>
    </div>

    <!-- Checks Table -->
    <div class="rounded-xl bg-[#050914] border border-slate-800 overflow-hidden">
        <div class="divide-y divide-slate-800/80 text-xs">
            <?php foreach ($checks as $key => $check): ?>
                <div class="p-3.5 flex items-center justify-between hover:bg-slate-800/10 transition-colors">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-slate-200"><?= esc($check['title']) ?></span>
                            <?php if ($check['critical']): ?>
                                <span class="text-[10px] text-slate-500 font-mono">REQUIRED</span>
                            <?php else: ?>
                                <span class="text-[10px] text-slate-500 font-mono">RECOMMENDED</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5"><?= esc($check['description']) ?> (Detected: <span class="font-mono text-slate-300"><?= esc($check['current']) ?></span>)</p>
                    </div>
                    <div>
                        <?php if ($check['status'] === 'passed'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 font-semibold text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Passed
                            </span>
                        <?php elseif ($check['status'] === 'warning'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-950/60 border border-amber-500/30 text-amber-300 font-semibold text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                Warning
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-950/60 border border-rose-500/30 text-rose-300 font-semibold text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                Failed
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Navigation / Continue Bar -->
    <div class="pt-2 flex items-center justify-between">
        <a href="<?= site_url('install') ?>" class="text-xs text-slate-400 hover:text-white transition-colors">
            &larr; Back
        </a>

        <?php if ($all_passed): ?>
            <a href="<?= site_url('install/database') ?>" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)] flex items-center gap-2">
                <span>Continue to Database</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        <?php else: ?>
            <div class="flex items-center gap-3">
                <span class="text-xs text-rose-400 font-medium">Resolve failed critical items to continue.</span>
                <button disabled class="px-6 py-3 rounded-xl bg-slate-800 text-slate-500 font-semibold text-xs cursor-not-allowed">
                    Continue to Database
                </button>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
