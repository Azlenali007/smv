<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[75vh] flex items-center justify-center px-4 sm:px-6 py-12">
    
    <div class="max-w-md w-full rounded-2xl bg-[#090E1E] border border-slate-800/80 p-8 shadow-2xl relative overflow-hidden backdrop-blur-md" x-data="{ showPass: false, loading: false }">
        
        <div class="text-center mb-8 space-y-2">
            <h1 class="text-2xl font-bold text-white tracking-tight">Sign In to <?= esc($site_name) ?></h1>
            <p class="text-xs text-slate-400">Enter your credentials to access your client dashboard.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-3 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="p-3 mb-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 text-xs">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('login') ?>" method="POST" @submit="loading = true" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Email Address</label>
                <input type="email" name="email" value="<?= old('email') ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="name@example.com">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-medium text-slate-300">Password</label>
                    <a href="<?= site_url('forgot-password') ?>" class="text-[11px] text-blue-400 hover:text-blue-300">Forgot?</a>
                </div>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 pr-10" placeholder="••••••••">
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs">
                        <span x-text="showPass ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
            </div>

            <button type="submit" :disabled="loading" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] disabled:opacity-50">
                <span x-show="!loading">Sign In</span>
                <span x-show="loading" x-cloak>Verifying credentials...</span>
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-400">
            Don't have an account? <a href="<?= site_url('register') ?>" class="text-blue-400 hover:text-blue-300 font-semibold">Sign Up</a>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
