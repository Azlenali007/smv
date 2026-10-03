<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="space-y-8">

    <!-- Top Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="p-6 rounded-2xl bg-[#090E1F] border border-slate-800/80 space-y-2">
            <span class="text-xs text-slate-400 font-medium">Available Balance (Display)</span>
            <div class="text-2xl font-extrabold text-white font-mono tabular-nums">
                <?= $currency_service->format($currency_service->convertFromInr($wallet['balance'] ?? 0, $current_currency), $current_currency) ?>
            </div>
            <p class="text-[11px] text-slate-500 font-mono">
                Base: ₹<?= number_format((float)($wallet['balance'] ?? 0), 2) ?> INR
            </p>
        </div>

        <div class="p-6 rounded-2xl bg-[#090E1F] border border-slate-800/80 space-y-2">
            <span class="text-xs text-slate-400 font-medium">Accounting Guarantee</span>
            <div class="text-base font-bold text-emerald-400">Atomic MySQL Ledger</div>
            <p class="text-xs text-slate-400">Zero floating point rounding. Strict DECIMAL(16,4) ledger.</p>
        </div>

        <div class="p-6 rounded-2xl bg-[#090E1F] border border-slate-800/80 space-y-2">
            <span class="text-xs text-slate-400 font-medium">Payment Protocol</span>
            <div class="text-base font-bold text-blue-400">Server-Verified Credit</div>
            <p class="text-xs text-slate-400">Wallets credit only upon verified webhook/server-to-server confirmation.</p>
        </div>
    </div>

    <!-- Add Funds Section -->
    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6 sm:p-8" x-data="{
        gateways: <?= htmlspecialchars(json_encode($gateways), ENT_QUOTES, 'UTF-8') ?>,
        selectedGatewayId: '<?= !empty($gateways) ? $gateways[0]['id'] : '' ?>',
        amount: 500,
        selectedGateway: null,

        init() {
            if (this.selectedGatewayId) {
                this.selectedGateway = this.gateways.find(g => g.id == this.selectedGatewayId);
            }
        },

        onGatewaySelect(id) {
            this.selectedGatewayId = id;
            this.selectedGateway = this.gateways.find(g => g.id == id);
        }
    }">
        <h2 class="text-lg font-bold text-white tracking-tight mb-1">Add Funds to Wallet</h2>
        <p class="text-xs text-slate-400 mb-6">Select an active gateway and specify deposit amount.</p>

        <?php if (!empty($gateways)): ?>
            <form action="<?= site_url('wallet/deposit') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Gateway Cards -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Select Payment Method</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <template x-for="g in gateways" :key="g.id">
                            <div @click="onGatewaySelect(g.id)" 
                                 :class="(selectedGatewayId == g.id) ? 'border-blue-500 bg-blue-950/20 text-white shadow-[0_0_15px_rgba(59,130,246,0.2)]' : 'border-slate-800 bg-[#050914] text-slate-300 hover:border-slate-700'"
                                 class="p-4 rounded-xl border cursor-pointer transition-all space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold" x-text="g.name"></span>
                                    <span class="text-[10px] font-mono text-slate-500" x-text="g.currency_code"></span>
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    Fee: <span class="font-mono text-slate-300" x-text="g.fee_percentage + '%'"></span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono">
                                    Min: <span x-text="parseFloat(g.min_amount).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="gateway_id" :value="selectedGatewayId">
                </div>

                <!-- Amount Field -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Amount in <span class="text-blue-400 font-mono" x-text="selectedGateway ? selectedGateway.currency_code : 'INR'"></span>
                    </label>
                    <input type="number" name="amount" x-model="amount" required step="0.01" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                </div>

                <!-- Gateway Instructions if any -->
                <div x-show="selectedGateway && selectedGateway.instructions" x-cloak class="p-3.5 rounded-xl bg-[#060B19] border border-slate-800 text-xs text-slate-400 space-y-1">
                    <span class="text-[11px] font-semibold text-blue-400 block">Payment Instructions:</span>
                    <p x-text="selectedGateway ? selectedGateway.instructions : ''" class="whitespace-pre-line leading-relaxed"></p>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)]">
                    Initiate Verified Deposit
                </button>
            </form>
        <?php else: ?>
            <div class="rounded-xl border border-dashed border-slate-800 p-8 text-center bg-[#060914]">
                <p class="text-xs font-medium text-slate-300">Payment Gateways Pending Configuration</p>
                <p class="text-[11px] text-slate-500 mt-1">Administrators have not yet enabled public payment gateways. Contact support for manual bank/crypto options.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Transactions & Payment History -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Wallet Ledger -->
        <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 overflow-hidden">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Wallet Ledger</h3>
                <a href="<?= site_url('wallet/transactions') ?>" class="text-[11px] text-blue-400 hover:text-blue-300">Full Ledger &rarr;</a>
            </div>
            <?php if (!empty($transactions)): ?>
                <div class="divide-y divide-slate-800/60 text-xs">
                    <?php foreach ($transactions as $t): ?>
                        <div class="p-3.5 hover:bg-slate-800/20 flex items-center justify-between">
                            <div>
                                <span class="font-semibold block text-slate-200"><?= esc($t['remarks'] ?? ucfirst($t['type'])) ?></span>
                                <span class="text-[10px] text-slate-500 font-mono"><?= date('M d, Y H:i', strtotime($t['created_at'])) ?> · Ref: <?= esc($t['reference_id'] ?? 'N/A') ?></span>
                            </div>
                            <div class="text-right">
                                <?php $isCredit = in_array($t['type'], ['deposit', 'refund']) || ((float)$t['amount'] > 0 && $t['type'] === 'manual_adjustment'); ?>
                                <span class="font-mono font-bold tabular-nums block <?= $isCredit ? 'text-emerald-400' : 'text-slate-300' ?>">
                                    <?= $isCredit ? '+' : '-' ?>₹<?= number_format(abs((float)$t['amount']), 2) ?>
                                </span>
                                <span class="text-[10px] text-slate-500 font-mono">Bal: ₹<?= number_format((float)$t['closing_balance'], 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-xs text-slate-500">No balance movements recorded yet.</div>
            <?php endif; ?>
        </div>

        <!-- Payment Gateway Receipts -->
        <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 overflow-hidden">
            <div class="p-4 border-b border-slate-800">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Deposit Gateway Invoices</h3>
            </div>
            <?php if (!empty($payments)): ?>
                <div class="divide-y divide-slate-800/60 text-xs">
                    <?php foreach ($payments as $p): ?>
                        <div class="p-3.5 hover:bg-slate-800/20 flex items-center justify-between">
                            <div>
                                <span class="font-semibold block text-slate-200"><?= esc($p['gateway_name'] ?? 'Payment Gateway') ?></span>
                                <span class="text-[10px] text-slate-500 font-mono"><?= esc($p['transaction_reference']) ?> · <?= date('M d, Y', strtotime($p['created_at'])) ?></span>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-white block">
                                    <?= esc($p['currency']) ?> <?= number_format((float)$p['amount'], 2) ?>
                                </span>
                                <span class="text-[10px] font-semibold <?= ($p['status'] === 'completed') ? 'text-emerald-400' : 'text-amber-400' ?>">
                                    <?= ucfirst(esc($p['status'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-xs text-slate-500">No payment invoices initiated yet.</div>
            <?php endif; ?>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
