<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    selectAll: false,
    selected: [],
    margin: 20,
    targetCategory: '<?= !empty($categories) ? $categories[0]['id'] : '' ?>',

    toggleAll() {
        if (this.selectAll) {
            this.selected = <?= htmlspecialchars(json_encode(array_column($cachedServices, 'remote_service_id')), ENT_QUOTES, 'UTF-8') ?>;
        } else {
            this.selected = [];
        }
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Import Services from <?= esc($provider['name']) ?></h1>
            <p class="text-xs text-slate-400 mt-1">Found <?= count($cachedServices) ?> services from the provider's live API response.</p>
        </div>
        <a href="<?= site_url('admin/providers') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Providers
        </a>
    </div>

    <!-- Import Controls Bar -->
    <form action="<?= site_url('admin/providers/' . $provider['id'] . '/import') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <div class="p-5 rounded-2xl bg-[#080D1D] border border-slate-800/80 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Destination Category</label>
                <select name="target_category_id" x-model="targetCategory" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= esc($c['id']) ?>"><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Profit Margin Percentage</label>
                <div class="flex items-center gap-2">
                    <input type="number" step="0.5" name="margin_percentage" x-model="margin" required min="0" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    <span class="text-xs text-slate-400 font-semibold">%</span>
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit" :disabled="selected.length === 0" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_15px_rgba(37,99,235,0.3)] disabled:opacity-40 disabled:cursor-not-allowed">
                    Import <span x-text="selected.length"></span> Selected Services
                </button>
            </div>
        </div>

        <!-- Fetched Services List -->
        <?php if (!empty($cachedServices)): ?>
            <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-4 w-10">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-800 bg-[#050914] text-blue-600 focus:ring-0">
                                </th>
                                <th class="py-3 px-4">Remote ID</th>
                                <th class="py-3 px-4">Service Name</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4 font-mono text-right">Provider Rate (<?= esc($provider['currency']) ?>)</th>
                                <th class="py-3 px-4 font-mono text-right">Estimated Selling Price (INR)</th>
                                <th class="py-3 px-4 font-mono text-right">Min / Max</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            <?php foreach ($cachedServices as $cs): ?>
                                <tr class="hover:bg-slate-800/20 transition-colors">
                                    <td class="py-3 px-4">
                                        <input type="checkbox" name="selected_services[]" value="<?= esc($cs['remote_service_id']) ?>" x-model="selected" class="rounded border-slate-800 bg-[#050914] text-blue-600 focus:ring-0">
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500">#<?= esc($cs['remote_service_id']) ?></td>
                                    <td class="py-3 px-4 font-semibold text-white max-w-sm truncate"><?= esc($cs['name']) ?></td>
                                    <td class="py-3 px-4 text-slate-400"><?= esc($cs['category']) ?></td>
                                    <td class="py-3 px-4 font-mono text-right font-medium text-slate-200 tabular-nums">
                                        <?= esc($provider['currency']) ?> <?= number_format((float)$cs['rate'], 4) ?>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-right font-bold text-blue-400 tabular-nums">
                                        <?php 
                                            // Pipeline demonstration: Provider Rate -> INR -> Margin
                                            $calc = $currency_service->calculateSellingRate($cs['rate'], $provider['currency'], 20.0, 'INR');
                                        ?>
                                        ₹<?= $calc['rate_in_inr'] ?> INR
                                    </td>
                                    <td class="py-3 px-4 font-mono text-right text-slate-400 tabular-nums">
                                        <?= number_format($cs['min']) ?> / <?= number_format($cs['max']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#080D1D]">
                <p class="text-sm font-semibold text-slate-300">No Services Returned by Provider</p>
                <p class="text-xs text-slate-500 mt-1">The provider did not return any service records, or the API call failed.</p>
            </div>
        <?php endif; ?>

    </form>

</div>
<?= $this->endSection() ?>
