<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[75vh] flex items-center justify-center px-4 sm:px-6 py-12">
    
    <div class="max-w-md w-full rounded-2xl bg-[#090E1E] border border-slate-800/80 p-8 shadow-2xl relative overflow-hidden backdrop-blur-md">
        
        <div class="text-center mb-6 space-y-2">
            <h1 class="text-2xl font-bold text-white tracking-tight">Recover Password</h1>
            <p class="text-xs text-slate-400">Enter your email and we will generate a recovery instruction token.</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="p-3 mb-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 text-xs">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('forgot-password') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Registered Email Address</label>
                <input type="email" name="email" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="name@example.com">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
                Send Recovery Instructions
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-400">
            Remembered password? <a href="<?= site_url('login') ?>" class="text-blue-400 hover:text-blue-300 font-semibold">Back to Login</a>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
