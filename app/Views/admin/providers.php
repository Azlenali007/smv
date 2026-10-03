<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    modalOpen: false,
    editMode: false,
    currentProvider: { id: '', name: '', api_url: '', api_key: '', currency: 'USD', status: 'active' },

    openNew() {
        this.editMode = false;
        this.currentProvider = { id: '', name: '', api_url: '', api_key: '', currency: 'USD', status: 'active' };
        this.modalOpen = true;
    },

    openEdit(p) {
        this.editMode = true;
        this.currentProvider = { id: p.id, name: p.name, api_url: p.api_url, api_key: '', currency: p.currency, status: p.status };
        this.modalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">API Providers &amp; Integrations</h1>
            <p class="text-xs text-slate-400 mt-1">Connect upstream SMM providers via standard SMM API v2 to test live balances and import services.</p>
        </div>
        <button @click="openNew()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
            + Add Provider
        </button>
    </div>

    <!-- Providers Table -->
    <?php if (!empty($providers)): ?>
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Provider Name</th>
                            <th class="py-3 px-4">API Endpoint</th>
                            <th class="py-3 px-4 font-mono text-right">Live Provider Balance</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 font-mono text-right">Last Verified</th>
                            <th class="py-3 px-4 text-right">Integration Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($providers as $p): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-500">#<?= esc($p['id']) ?></td>
                                <td class="py-3.5 px-4 font-bold text-white"><?= esc($p['name']) ?></td>
                                <td class="py-3.5 px-4 font-mono text-slate-400 max-w-xs truncate"><?= esc($p['api_url']) ?></td>
                                <td class="py-3.5 px-4 font-mono text-right font-bold text-emerald-400 tabular-nums">
                                    <?= esc($p['currency']) ?> <?= number_format((float)$p['balance'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-xs font-semibold <?= ($p['status'] === 'active') ? 'text-emerald-400' : 'text-slate-500' ?> uppercase">
                                        <?= esc($p['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-500 text-[11px]">
                                    <?= !empty($p['last_sync_at']) ? date('M d, H:i', strtotime($p['last_sync_at'])) : 'Never Tested' ?>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <!-- Test Connection Button -->
                                    <form action="<?= site_url('admin/providers/' . $p['id'] . '/test') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 hover:bg-emerald-900/50 text-xs font-medium">
                                            Test Balance
                                        </button>
                                    </form>

                                    <!-- Get Services Button -->
                                    <a href="<?= site_url('admin/providers/' . $p['id'] . '/services') ?>" class="px-2.5 py-1 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-semibold">
                                        Get Services &amp; Import
                                    </a>

                                    <button @click="openEdit(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs">
                                        Edit
                                    </button>

                                    <form action="<?= site_url('admin/providers/' . $p['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Delete provider?');">
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
            <p class="text-sm font-semibold text-slate-300">No API Providers Added</p>
            <p class="text-xs text-slate-500 mt-1">Connect your first provider using their API URL and Key above.</p>
        </div>
    <?php endif; ?>

    <!-- Provider Configuration Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-[#0A0F21] border border-slate-800 rounded-2xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-sm font-bold text-white tracking-tight" x-text="editMode ? 'Edit Provider Settings' : 'Add New SMM Provider'"></h3>

            <form :action="editMode ? '<?= site_url('admin/providers') ?>/' + currentProvider.id + '/update' : '<?= site_url('admin/providers/store') ?>'" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Provider Name</label>
                    <input type="text" name="name" x-model="currentProvider.name" required placeholder="e.g. PeakProvider" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">API Endpoint URL</label>
                    <input type="url" name="api_url" x-model="currentProvider.api_url" required placeholder="https://provider.com/api/v2" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        API Key <span x-show="editMode" class="text-slate-500 text-[10px]">(leave blank to keep unchanged)</span>
                    </label>
                    <input type="password" name="api_key" x-model="currentProvider.api_key" :required="!editMode" placeholder="Sensitive API Secret" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Provider Currency</label>
                        <input type="text" name="currency" x-model="currentProvider.currency" required placeholder="USD" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status</label>
                        <select name="status" x-model="currentProvider.status" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-xs text-slate-300">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">Save Provider</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
