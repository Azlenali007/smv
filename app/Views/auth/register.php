<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 py-12">
    
    <div class="max-w-md w-full rounded-2xl bg-[#090E1E] border border-slate-800/80 p-8 shadow-2xl relative overflow-hidden backdrop-blur-md" x-data="{ showPass: false, loading: false }">
        
        <div class="text-center mb-6 space-y-2">
            <h1 class="text-2xl font-bold text-white tracking-tight">Create Account</h1>
            <p class="text-xs text-slate-400">Join <?= esc($site_name) ?> for direct API access and instant ordering.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-3 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="p-3 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <p><?= esc($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('register') ?>" method="POST" @submit="loading = true" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Full Name</label>
                <input type="text" name="name" value="<?= old('name') ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="John Doe">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Email Address</label>
                <input type="email" name="email" value="<?= old('email') ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="name@example.com">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Password (Min. 8 characters)</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required minlength="8" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 pr-10" placeholder="••••••••">
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs">
                        <span x-text="showPass ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Confirm Password</label>
                <input :type="showPass ? 'text' : 'password'" name="password_confirmation" required minlength="8" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" placeholder="••••••••">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="terms" value="1" id="terms" required class="rounded border-slate-800 bg-[#050914] text-blue-600 focus:ring-0">
                <label for="terms" class="text-xs text-slate-400">
                    I agree to the <a href="<?= site_url('terms') ?>" target="_blank" class="text-blue-400 hover:underline">Terms of Service</a>
                </label>
            </div>

            <button type="submit" :disabled="loading" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] disabled:opacity-50">
                <span x-show="!loading">Create My Account</span>
                <span x-show="loading" x-cloak>Setting up account...</span>
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-400">
            Already have an account? <a href="<?= site_url('login') ?>" class="text-blue-400 hover:text-blue-300 font-semibold">Sign In</a>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
