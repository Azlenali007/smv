<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    modalOpen: false,
    editMode: false,
    currentService: {
        id: '',
        category_id: '',
        provider_id: '',
        provider_service_id: '',
        name: '',
        description: '',
        min_quantity: 10,
        max_quantity: 100000,
        rate_per_1k: '0.0000',
        margin_percentage: '15.00',
        provider_rate: '',
        provider_currency: 'USD',
        status: 'active',
        sort_order: 0
    },

    openNew() {
        this.editMode = false;
        this.currentService = {
            id: '',
            category_id: '<?= !empty($categories) ? $categories[0]['id'] : '' ?>',
            provider_id: '',
            provider_service_id: '',
            name: '',
            description: '',
            min_quantity: 10,
            max_quantity: 100000,
            rate_per_1k: '50.0000',
            margin_percentage: '15.00',
            provider_rate: '',
            provider_currency: 'USD',
            status: 'active',
            sort_order: 0
        };
        this.modalOpen = true;
    },

    openEdit(s) {
        this.editMode = true;
        this.currentService = Object.assign({}, s);
        this.modalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Service Catalog Management</h1>
            <p class="text-xs text-slate-400 mt-1">Configure pricing rates (in INR base), profit margins, and automated API provider bindings.</p>
        </div>
        <button @click="openNew()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
            + New Service
        </button>
    </div>

    <!-- Services Table -->
    <?php if (!empty($services)): ?>
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Provider Link</th>
                            <th class="py-3 px-4 font-mono text-right">Selling Rate / 1k (INR)</th>
                            <th class="py-3 px-4 font-mono text-right">Min / Max</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($services as $s): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-500">#<?= esc($s['id']) ?></td>
                                <td class="py-3.5 px-4 font-semibold text-white max-w-xs truncate">
                                    <?= esc($s['name']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    <?= esc($s['category_name'] ?? 'Uncategorized') ?>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                    <?php if (!empty($s['provider_name'])): ?>
                                        <span class="text-blue-400 font-semibold"><?= esc($s['provider_name']) ?></span> (ID: <?= esc($s['provider_service_id']) ?>)
                                    <?php else: ?>
                                        <span class="text-slate-600">Manual / Custom</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right font-bold text-blue-400 tabular-nums">
                                    ₹<?= number_format((float)$s['rate_per_1k'], 2) ?>
                                    <span class="text-[10px] text-slate-500 block">+<?= number_format((float)$s['margin_percentage'], 1) ?>% margin</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-400 tabular-nums">
                                    <?= number_format($s['min_quantity']) ?> / <?= number_format($s['max_quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-xs font-semibold <?= ($s['status'] === 'active') ? 'text-emerald-400' : 'text-slate-500' ?> uppercase">
                                        <?= esc($s['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <button @click="openEdit(<?= htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8') ?>)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs">
                                        Edit
                                    </button>
                                    <form action="<?= site_url('admin/services/' . $s['id'] . '/toggle') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs">
                                            <?= ($s['status'] === 'active') ? 'Disable' : 'Enable' ?>
                                        </button>
                                    </form>
                                    <form action="<?= site_url('admin/services/' . $s['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Permanently remove this service?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs">
                                            Del
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#080D1D]">
            <p class="text-sm font-semibold text-slate-300">No Services Configured</p>
            <p class="text-xs text-slate-500 mt-1">Manually configure services or import in bulk from an automated Provider.</p>
        </div>
    <?php endif; ?>

    <!-- Service Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm overflow-y-auto">
        <div @click.away="modalOpen = false" class="bg-[#0A0F21] border border-slate-800 rounded-2xl max-w-xl w-full p-6 space-y-4 my-8">
            <h3 class="text-sm font-bold text-white tracking-tight" x-text="editMode ? 'Edit Service #' + currentService.id : 'Create New Service'"></h3>

            <form :action="editMode ? '<?= site_url('admin/services') ?>/' + currentService.id + '/update' : '<?= site_url('admin/services/store') ?>'" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Category</label>
                        <select name="category_id" x-model="currentService.category_id" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= esc($c['id']) ?>"><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Provider Connection</label>
                        <select name="provider_id" x-model="currentService.provider_id" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Manual / No Provider</option>
                            <?php foreach ($providers as $p): ?>
                                <option value="<?= esc($p['id']) ?>"><?= esc($p['name']) ?> (<?= esc($p['currency']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Service Name</label>
                        <input type="text" name="name" x-model="currentService.name" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Provider Service ID</label>
                        <input type="text" name="provider_service_id" x-model="currentService.provider_service_id" placeholder="Remote ID from provider" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Rate / 1k (INR Base)</label>
                        <input type="number" step="0.0001" name="rate_per_1k" x-model="currentService.rate_per_1k" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Min Quantity</label>
                        <input type="number" name="min_quantity" x-model="currentService.min_quantity" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Max Quantity</label>
                        <input type="number" name="max_quantity" x-model="currentService.max_quantity" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Description / Instructions</label>
                    <textarea name="description" x-model="currentService.description" rows="3" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-xs text-slate-300">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">Save Service</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
