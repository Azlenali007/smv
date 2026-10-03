<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10">
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-bold text-white tracking-tight">Platform FAQ</h1>
        <p class="text-xs text-slate-400">Everything you need to know about placing orders, wallet accounting, and API connectivity.</p>
    </div>

    <div class="space-y-4" x-data="{ active: 1 }">
        <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80">
            <h3 class="text-sm font-semibold text-white">How does order processing work?</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                When you submit an order, your wallet balance is verified and debited atomically. If the service is linked to an API provider, the system calls the upstream provider API immediately and saves the remote order reference.
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80">
            <h3 class="text-sm font-semibold text-white">What currency is used for internal accounting?</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                The core base currency of ApexPulse is Indian Rupees (INR). All provider costs and selling prices are maintained in INR with DECIMAL(16,4) arithmetic. When you select USD or EUR, rates are converted dynamically according to admin exchange rates.
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80">
            <h3 class="text-sm font-semibold text-white">How do I add funds to my account?</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                Navigate to the Add Funds section inside your user dashboard, pick an active payment method, and complete the transaction. Your balance will be updated automatically upon verified confirmation.
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
