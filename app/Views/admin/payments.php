<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    modalOpen: false,
    currentGateway: { id: '', name: '', code: '', min_amount: '100.0000', max_amount: '500000.0000', fee_percentage: '0.00', currency_code: 'INR', credentials: '', instructions: '', is_active: 0 },

    openEdit(g) {
        this.currentGateway = Object.assign({}, g);
        this.modalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Payment Gateways &amp; Processing</h1>
            <p class="text-xs text-slate-400 mt-1">Configure automated deposit methods, fees, and API merchant credentials.</p>
        </div>
        <a href="<?= site_url('admin/payments/transactions') ?>" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition-all">
            View Payment Invoices &rarr;
        </a>
    </div>

    <!-- Gateways Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($gateways as $g): ?>
            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white"><?= esc($g['name']) ?></span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded <?= ($g['is_active']) ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-500/20' : 'bg-slate-900 text-slate-500' ?>">
                            <?= ($g['is_active']) ? 'ACTIVE' : 'INACTIVE' ?>
                        </span>
                    </div>
                    <p class="text-xs font-mono text-slate-400">Code: <?= esc($g['code']) ?> (<?= esc($g['currency_code']) ?>)</p>
                    <div class="text-xs text-slate-400 space-y-1 pt-2 border-t border-slate-800">
                        <div>Fee: <span class="font-mono text-white"><?= esc($g['fee_percentage']) ?>%</span></div>
                        <div>Limits: <span class="font-mono text-white"><?= number_format((float)$g['min_amount'], 2) ?> - <?= number_format((float)$g['max_amount'], 2) ?></span></div>
                    </div>
                </div>

                <button @click="openEdit(<?= htmlspecialchars(json_encode($g), ENT_QUOTES, 'UTF-8') ?>)" class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold">
                    Configure Gateway &amp; API Keys
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Gateway Edit Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-[#0A0F21] border border-slate-800 rounded-2xl max-w-lg w-full p-6 space-y-4">
            <h3 class="text-sm font-bold text-white tracking-tight">Configure Payment Gateway: <span x-text="currentGateway.name"></span></h3>

            <form :action="'<?= site_url('admin/payments/gateway') ?>/' + currentGateway.id + '/update'" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Display Name</label>
                        <input type="text" name="name" x-model="currentGateway.name" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Currency</label>
                        <input type="text" name="currency_code" x-model="currentGateway.currency_code" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Min Deposit</label>
                        <input type="number" step="0.01" name="min_amount" x-model="currentGateway.min_amount" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Max Deposit</label>
                        <input type="number" step="0.01" name="max_amount" x-model="currentGateway.max_amount" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Fee %</label>
                        <input type="number" step="0.01" name="fee_percentage" x-model="currentGateway.fee_percentage" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Merchant Credentials (JSON format)</label>
                    <textarea name="credentials" x-model="currentGateway.credentials" rows="3" placeholder='{"merchant_key": "xxx", "salt": "yyy"}' class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Client Payment Instructions</label>
                    <textarea name="instructions" x-model="currentGateway.instructions" rows="2" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" id="gateway_active_toggle" :checked="currentGateway.is_active == 1" class="rounded border-slate-800 bg-[#050914] text-blue-600 focus:ring-0">
                    <label for="gateway_active_toggle" class="text-xs text-slate-300">Enable this gateway for client deposits</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-xs text-slate-300">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
