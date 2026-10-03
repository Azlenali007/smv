<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Services & Pricing</h1>
        <p class="text-xs text-slate-400 mt-1">Live rates and order limits for all active services in your display currency (<?= esc($current_currency) ?>).</p>
    </div>

    <!-- Filters & Search -->
    <form method="GET" action="<?= site_url('user-services') ?>" class="flex flex-col sm:flex-row gap-3">
        <div class="w-full sm:w-64">
            <select name="category" onchange="this.form.submit()" class="w-full bg-[#090E1F] border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-blue-500">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= esc($cat['id']) ?>" <?= ($selectedCategory == $cat['id']) ? 'selected' : '' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex-1 flex gap-2">
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search services..." class="w-full bg-[#090E1F] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold">
                Filter
            </button>
            <?php if ($selectedCategory || $search): ?>
                <a href="<?= site_url('user-services') ?>" class="px-3 py-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-xs flex items-center">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Services Table -->
    <?php if (!empty($services)): ?>
        <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4 font-mono text-right">Rate / 1,000</th>
                            <th class="py-3 px-4 font-mono text-right">Min</th>
                            <th class="py-3 px-4 font-mono text-right">Max</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($services as $s): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-500">#<?= esc($s['id']) ?></td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-white max-w-sm truncate"><?= esc($s['name']) ?></div>
                                    <?php if (!empty($s['description'])): ?>
                                        <p class="text-[11px] text-slate-500 max-w-sm truncate mt-0.5"><?= esc($s['description']) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    <?= esc($s['category_name'] ?? 'General') ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-blue-400 font-bold tabular-nums">
                                    <?= $currency_service->format($currency_service->convertFromInr($s['rate_per_1k'], $current_currency), $current_currency) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-400 tabular-nums">
                                    <?= number_format($s['min_quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-400 tabular-nums">
                                    <?= number_format($s['max_quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= site_url('new-order?service=' . $s['id']) ?>" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold">
                                        Order
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#090E1F]">
            <p class="text-sm font-semibold text-slate-300">No Services Found</p>
            <p class="text-xs text-slate-500 mt-1">Please try another filter query.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
