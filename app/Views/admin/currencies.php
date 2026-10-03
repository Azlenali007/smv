<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    modalOpen: false,
    editMode: false,
    currentCurrency: { id: '', name: '', code: '', symbol: '', rate_to_inr: '1.000000', decimal_precision: 2, is_active: 1 },

    openNew() {
        this.editMode = false;
        this.currentCurrency = { id: '', name: '', code: '', symbol: '', rate_to_inr: '86.500000', decimal_precision: 2, is_active: 1 };
        this.modalOpen = true;
    },

    openEdit(c) {
        this.editMode = true;
        this.currentCurrency = Object.assign({}, c);
        this.modalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Multi-Currency & Exchange Rates</h1>
            <p class="text-xs text-slate-400 mt-1">
                Internal base is strictly <strong class="text-blue-400">INR (₹)</strong>. Rates represent how many INR equal 1 unit of foreign currency (e.g. 1 USD = 86.500000 INR).
            </p>
        </div>
        <button @click="openNew()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
            + Add Currency
        </button>
    </div>

    <!-- Currency Cards / Table -->
    <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Symbol</th>
                        <th class="py-3 px-4 font-mono text-right">Exchange Rate to 1 Unit (in INR)</th>
                        <th class="py-3 px-4 font-mono text-center">Decimals</th>
                        <th class="py-3 px-4">Role / Default</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <?php foreach ($currencies as $c): ?>
                        <tr class="hover:bg-slate-800/20 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-white"><?= esc($c['code']) ?></td>
                            <td class="py-3.5 px-4 font-medium text-slate-200"><?= esc($c['name']) ?></td>
                            <td class="py-3.5 px-4 font-bold text-blue-400"><?= esc($c['symbol']) ?></td>
                            <td class="py-3.5 px-4 font-mono text-right font-bold text-slate-100 tabular-nums">
                                <?php if ($c['code'] === 'INR'): ?>
                                    <span class="text-blue-400">1.000000 (Base)</span>
                                <?php else: ?>
                                    1 <?= esc($c['code']) ?> = ₹<?= number_format((float)$c['rate_to_inr'], 4) ?> INR
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-center tabular-nums"><?= esc($c['decimal_precision']) ?></td>
                            <td class="py-3.5 px-4">
                                <?php if (!empty($c['is_default'])): ?>
                                    <span class="text-xs font-semibold text-emerald-400">Default Display</span>
                                <?php else: ?>
                                    <form action="<?= site_url('admin/currencies/' . $c['id'] . '/set-default') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-[11px] text-slate-500 hover:text-slate-300 underline">
                                            Make Default
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-xs font-semibold <?= ($c['is_active']) ? 'text-emerald-400' : 'text-slate-500' ?> uppercase">
                                    <?= ($c['is_active']) ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button @click="openEdit(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs">
                                    Edit Rate
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Currency Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-[#0A0F21] border border-slate-800 rounded-2xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-sm font-bold text-white tracking-tight" x-text="editMode ? 'Edit Currency ' + currentCurrency.code : 'Configure New Currency'"></h3>

            <form :action="editMode ? '<?= site_url('admin/currencies') ?>/' + currentCurrency.id + '/update' : '<?= site_url('admin/currencies/store') ?>'" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Currency Name</label>
                        <input type="text" name="name" x-model="currentCurrency.name" required placeholder="US Dollar" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">ISO Code</label>
                        <input type="text" name="code" x-model="currentCurrency.code" :readonly="editMode" required placeholder="USD" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Symbol</label>
                        <input type="text" name="symbol" x-model="currentCurrency.symbol" required placeholder="$" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Decimal Precision</label>
                        <input type="number" name="decimal_precision" x-model="currentCurrency.decimal_precision" required min="0" max="4" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Exchange Rate (INR equivalent for 1 unit)
                    </label>
                    <input type="number" step="0.000001" name="rate_to_inr" x-model="currentCurrency.rate_to_inr" required :readonly="currentCurrency.code === 'INR'" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    <p class="text-[10px] text-slate-500 mt-1">Example: For USD, if 1 USD = 86.5 INR, enter 86.500000</p>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" id="is_active_toggle" :checked="currentCurrency.is_active == 1" class="rounded border-slate-800 bg-[#050914] text-blue-600 focus:ring-0">
                    <label for="is_active_toggle" class="text-xs text-slate-300">Active currency available for client display</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-xs text-slate-300">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
