<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto py-4 space-y-8">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Account Settings & Profile</h1>
        <p class="text-xs text-slate-400 mt-1">Manage personal contact information, security credentials, and preferred display currency.</p>
    </div>

    <!-- 1. Profile Information -->
    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6 sm:p-8 space-y-6">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Personal Information</h2>
        
        <form action="<?= site_url('profile/update') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Full Name</label>
                <input type="text" name="name" value="<?= esc($user['name']) ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                <input type="email" name="email" value="<?= esc($user['email']) ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                Save Profile Changes
            </button>
        </form>
    </div>

    <!-- 2. Display Currency Preference -->
    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Currency Preference</h2>
            <p class="text-xs text-slate-400 mt-1">Saved to your account in MySQL and automatically restored on future sessions.</p>
        </div>

        <form action="<?= site_url('profile/set-currency') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Selected Display Currency</label>
                <select name="currency" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    <?php foreach ($currencies as $c): ?>
                        <option value="<?= esc($c['code']) ?>" <?= ($c['code'] === ($user['currency_preference'] ?? 'INR')) ? 'selected' : '' ?>>
                            <?= esc($c['code']) ?> (<?= esc($c['symbol']) ?>) - <?= esc($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                Update Currency Preference
            </button>
        </form>
    </div>

    <!-- 3. Change Password -->
    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6 sm:p-8 space-y-6" x-data="{ showPw: false }">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Security & Password</h2>

        <form action="<?= site_url('profile/change-password') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Current Password</label>
                <input :type="showPw ? 'text' : 'password'" name="current_password" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">New Password (Min. 8 characters)</label>
                <input :type="showPw ? 'text' : 'password'" name="new_password" required minlength="8" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Confirm New Password</label>
                <input :type="showPw ? 'text' : 'password'" name="password_confirmation" required minlength="8" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="showPwToggle" @change="showPw = !showPw" class="rounded border-slate-800 bg-[#050914] text-blue-600 focus:ring-0">
                <label for="showPwToggle" class="text-xs text-slate-400">Show passwords</label>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                Change Password
            </button>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
