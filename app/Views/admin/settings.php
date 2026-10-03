<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">System Settings & Dynamic Branding</h1>
        <p class="text-xs text-slate-400 mt-1">Configure global application branding, maintenance controls, and default currency rules.</p>
    </div>

    <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 p-6 sm:p-8">
        
        <form action="<?= site_url('admin/settings/update') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Branding Section -->
            <div class="space-y-4">
                <h2 class="text-xs font-bold text-white uppercase tracking-wider border-b border-slate-800 pb-2">Dynamic Branding</h2>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Site Name / Brand Title</label>
                    <input type="text" name="site_name" value="<?= esc($settings['site_name'] ?? 'ApexPulse') ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Platform Tagline & Meta Description</label>
                    <textarea name="site_description" rows="2" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500"><?= esc($settings['site_description'] ?? 'Premium Next-Generation SMM Growth Platform') ?></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Public Support Email</label>
                        <input type="email" name="contact_email" value="<?= esc($settings['contact_email'] ?? 'support@apexpulse.io') ?>" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Display Currency</label>
                        <select name="default_currency" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                            <?php foreach ($currencies as $c): ?>
                                <option value="<?= esc($c['code']) ?>" <?= (($settings['default_currency'] ?? 'INR') === $c['code']) ? 'selected' : '' ?>>
                                    <?= esc($c['code']) ?> (<?= esc($c['symbol']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Maintenance Mode Section -->
            <div class="space-y-4 pt-4 border-t border-slate-800">
                <h2 class="text-xs font-bold text-white uppercase tracking-wider border-b border-slate-800 pb-2">Platform Maintenance Mode</h2>
                
                <div class="p-4 rounded-xl bg-[#060A16] border border-slate-800 space-y-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="maintenance_mode" value="1" id="maint_toggle" <?= (!empty($settings['maintenance_mode']) && $settings['maintenance_mode'] === '1') ? 'checked' : '' ?> class="rounded border-slate-800 bg-[#050914] text-blue-600 focus:ring-0">
                        <label for="maint_toggle" class="text-xs font-semibold text-white cursor-pointer">
                            Enable Platform Maintenance Mode
                        </label>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        When enabled, all public landing and user panel endpoints return HTTP 503 Maintenance. Administrator sessions bypass this barrier automatically.
                    </p>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Maintenance Notification Message</label>
                        <input type="text" name="maintenance_message" value="<?= esc($settings['maintenance_message'] ?? 'We are undergoing scheduled engineering upgrades. We will return online shortly.') ?>" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_20px_rgba(37,99,235,0.35)]">
                Save Site Configurations
            </button>
        </form>

    </div>

</div>
<?= $this->endSection() ?>
