<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{ modalOpen: false, editMode: false, currentCategory: { id: '', name: '', icon: '', sort_order: 0, status: 'active' } }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Category Management</h1>
            <p class="text-xs text-slate-400 mt-1">Organize service groups for user browsing and provider imports.</p>
        </div>
        <button @click="editMode = false; currentCategory = { id: '', name: '', icon: '', sort_order: 0, status: 'active' }; modalOpen = true" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
            + New Category
        </button>
    </div>

    <!-- Categories Table -->
    <?php if (!empty($categories)): ?>
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Category Name</th>
                            <th class="py-3 px-4 font-mono text-center">Sort Order</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-500">#<?= esc($cat['id']) ?></td>
                                <td class="py-3.5 px-4 font-semibold text-white"><?= esc($cat['name']) ?></td>
                                <td class="py-3.5 px-4 font-mono text-center tabular-nums"><?= esc($cat['sort_order']) ?></td>
                                <td class="py-3.5 px-4">
                                    <span class="text-xs font-semibold <?= ($cat['status'] === 'active') ? 'text-emerald-400' : 'text-slate-500' ?> uppercase">
                                        <?= esc($cat['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <button @click="editMode = true; currentCategory = <?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>; modalOpen = true" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium">
                                        Edit
                                    </button>
                                    <form action="<?= site_url('admin/categories/' . $cat['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category? Associated services will remain.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-950/40 border border-rose-500/30 text-rose-300 hover:bg-rose-900/50 text-xs font-medium">
                                            Delete
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
            <p class="text-sm font-semibold text-slate-300">No Categories Created</p>
            <p class="text-xs text-slate-500 mt-1">Create your first category (e.g. Instagram Followers, YouTube Views) above.</p>
        </div>
    <?php endif; ?>

    <!-- Category Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-[#0A0F21] border border-slate-800 rounded-2xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-sm font-bold text-white tracking-tight" x-text="editMode ? 'Edit Category' : 'Create New Category'"></h3>

            <form :action="editMode ? '<?= site_url('admin/categories') ?>/' + currentCategory.id + '/update' : '<?= site_url('admin/categories/store') ?>'" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Category Name</label>
                    <input type="text" name="name" x-model="currentCategory.name" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" x-model="currentCategory.sort_order" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status</label>
                    <select name="status" x-model="currentCategory.status" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-xs text-slate-300">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">Save Category</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
