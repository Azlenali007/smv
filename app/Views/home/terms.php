<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-8">
    <div class="space-y-2">
        <h1 class="text-3xl font-bold text-white tracking-tight">Terms of Service</h1>
        <p class="text-xs text-slate-400">Please review the operational terms and conditions governing the use of <?= esc($site_name) ?>.</p>
    </div>

    <div class="p-8 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-6 text-xs text-slate-300 leading-relaxed">
        <section class="space-y-2">
            <h2 class="text-sm font-semibold text-white">1. Account Responsibility</h2>
            <p>You are responsible for maintaining the confidentiality of your login credentials and for all activities that occur under your user account.</p>
        </section>

        <section class="space-y-2">
            <h2 class="text-sm font-semibold text-white">2. Order Placement & Fulfillment</h2>
            <p>By placing an order on <?= esc($site_name) ?>, you acknowledge that links provided must be public and compliant with all third-party platform terms. Once an order is processed by upstream providers, cancellations may be restricted based on service provider capabilities.</p>
        </section>

        <section class="space-y-2">
            <h2 class="text-sm font-semibold text-white">3. Wallet Transactions & Refunds</h2>
            <p>All deposited funds are credited to your platform wallet balance. Any eligible refunds for cancelled or failed orders will be returned directly to your internal wallet balance for future orders.</p>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
