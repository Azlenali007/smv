<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    
    <div>
        <h1 class="text-3xl font-bold text-white tracking-tight">Services & Pricing</h1>
        <p class="text-xs text-slate-400 mt-1">Browse our real database catalog of active social growth services.</p>
    </div>

    <!-- Filters & Search Bar -->
    <form method="GET" action="<?= site_url('services') ?>" class="flex flex-col sm:flex-row gap-3">
        <div class="w-full sm:w-64">
            <select name="category" onchange="this.form.submit()" class="w-full bg-[#080D1D] border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-blue-500">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= esc($cat['id']) ?>" <?= ($selectedCategory == $cat['id']) ? 'selected' : '' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex-1 flex gap-2">
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search services by keyword..." class="w-full bg-[#080D1D] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold">
                Filter
            </button>
            <?php if ($selectedCategory || $search): ?>
                <a href="<?= site_url('services') ?>" class="px-3.5 py-2.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-xs flex items-center">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Services Table -->
    <?php if (!empty($services)): ?>
        <div class="rounded-2xl border border-slate-800/80 overflow-hidden bg-[#080D1D]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4 font-mono text-right">Rate / 1k</th>
                            <th class="py-3 px-4 font-mono text-right">Min</th>
                            <th class="py-3 px-4 font-mono text-right">Max</th>
                            <th class="py-3 px-4 text-center">Description</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300" x-data="{ activeModal: null }">
                        <?php foreach ($services as $s): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-500">#<?= esc($s['id']) ?></td>
                                <td class="py-3.5 px-4 font-medium text-white max-w-sm truncate">
                                    <?= esc($s['name']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    <?= esc($s['category_name'] ?? 'General') ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-blue-400 font-semibold tabular-nums">
                                    <?= $currency_service->format($currency_service->convertFromInr($s['rate_per_1k'], $current_currency), $current_currency) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-400 tabular-nums">
                                    <?= number_format($s['min_quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-400 tabular-nums">
                                    <?= number_format($s['max_quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php if (!empty($s['description'])): ?>
                                        <button @click="activeModal = <?= esc($s['id']) ?>" class="text-[11px] text-blue-400 hover:underline">
                                            View
                                        </button>
                                        <!-- Modal -->
                                        <div x-show="activeModal === <?= esc($s['id']) ?>" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
                                            <div @click.away="activeModal = null" class="bg-[#0B1021] border border-slate-800 rounded-2xl max-w-md w-full p-6 text-left space-y-4">
                                                <h3 class="text-sm font-bold text-white"><?= esc($s['name']) ?></h3>
                                                <p class="text-xs text-slate-300 whitespace-pre-line leading-relaxed"><?= esc($s['description']) ?></p>
                                                <div class="text-right">
                                                    <button @click="activeModal = null" class="px-4 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-200">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-600">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= site_url('login') ?>" class="px-3 py-1 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-semibold">
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
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#070B18]">
            <p class="text-sm font-medium text-slate-300">No Services Found</p>
            <p class="text-xs text-slate-500 mt-1">Try adjusting your category filter or search query.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
