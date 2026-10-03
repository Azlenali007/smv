<?= $this->extend('installer/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    password: '',
    passwordConfirmation: '',
    showPass: false,
    submitting: false,
    
    get hasMinLength() { return this.password.length >= 8; },
    get passwordsMatch() { return this.password && this.password === this.passwordConfirmation; },
    get canSubmit() { return this.hasMinLength && this.passwordsMatch && !this.submitting; }
}">

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Step 5: Primary Administrator Setup</h2>
        <p class="text-xs text-slate-400 mt-1">This account will hold full superadmin credentials for the control console.</p>
    </div>

    <form action="<?= site_url('install/admin') ?>" method="POST" @submit="submitting = true" class="space-y-4">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Administrator Full Name</label>
                <input type="text" name="name" value="<?= esc(old('name', 'Master Administrator')) ?>" required placeholder="Master Administrator" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Administrator Email</label>
                <input type="email" name="email" value="<?= esc(old('email', 'admin@apexpulse.io')) ?>" required placeholder="admin@apexpulse.io" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password (Min. 8 characters)</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" x-model="password" required minlength="8" placeholder="••••••••" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 pr-12 font-mono">
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs">
                        <span x-text="showPass ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Confirm Password</label>
                <input :type="showPass ? 'text' : 'password'" name="password_confirmation" x-model="passwordConfirmation" required minlength="8" placeholder="••••••••" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            </div>
        </div>

        <!-- Password Validation Feedback -->
        <div class="p-3.5 rounded-xl bg-[#050914] border border-slate-800 text-xs space-y-1 font-mono">
            <div class="flex items-center gap-2" :class="hasMinLength ? 'text-emerald-400' : 'text-slate-500'">
                <span x-text="hasMinLength ? '✓' : '○'"></span>
                <span>Minimum 8 characters</span>
            </div>
            <div class="flex items-center gap-2" :class="passwordsMatch ? 'text-emerald-400' : 'text-slate-500'">
                <span x-text="passwordsMatch ? '✓' : '○'"></span>
                <span>Passwords match</span>
            </div>
        </div>

        <!-- Installation Notice -->
        <div class="p-4 rounded-xl bg-blue-950/20 border border-blue-500/20 text-xs text-slate-300 space-y-1">
            <span class="font-bold text-blue-400 block text-[11px] uppercase tracking-wider">Next: Step 6 &amp; 7 Execution</span>
            <p class="text-slate-400 text-[11px] leading-relaxed">
                Saving this administrator account will move to the live migration runner, where all 14 MySQL tables, baseline INR currencies, system settings, and environment files will be installed.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="pt-4 flex items-center justify-between border-t border-slate-800/80">
            <a href="<?= site_url('install/configuration') ?>" class="text-xs text-slate-400 hover:text-white transition-colors">
                &larr; Back to Config
            </a>

            <button type="submit" :disabled="!canSubmit" class="px-7 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.4)] disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                <span>Continue to Database Installation &rarr;</span>
            </button>
        </div>
    </form>

</div>
<?= $this->endSection() ?>
