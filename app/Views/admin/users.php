<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Client Account Directory</h1>
            <p class="text-xs text-slate-400 mt-1">Audit user credentials, balances, and operational statuses.</p>
        </div>
        
        <form method="GET" action="<?= site_url('admin/users') ?>" class="flex gap-2">
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search name, email, or user ID..." class="bg-[#080D1D] border border-slate-800 rounded-xl px-3.5 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold">Search</button>
            <?php if ($search): ?>
                <a href="<?= site_url('admin/users') ?>" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-xs flex items-center">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (!empty($users)): ?>
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">User ID</th>
                            <th class="py-3 px-4">Client Name</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4 font-mono text-right">Wallet Balance</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 font-mono text-right">Joined</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-400">#<?= esc($u['id']) ?></td>
                                <td class="py-3.5 px-4 font-semibold text-white"><?= esc($u['name']) ?></td>
                                <td class="py-3.5 px-4 font-mono text-slate-400"><?= esc($u['email']) ?></td>
                                <td class="py-3.5 px-4 font-mono text-right font-bold text-blue-400 tabular-nums">
                                    ₹<?= number_format((float)($u['wallet_balance'] ?? 0), 2) ?> INR
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-xs font-semibold <?= ($u['status'] === 'active') ? 'text-emerald-400' : 'text-rose-400' ?> uppercase">
                                        <?= esc($u['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-500 text-[11px]">
                                    <?= date('M d, Y', strtotime($u['created_at'])) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <a href="<?= site_url('admin/users/' . $u['id']) ?>" class="px-2.5 py-1 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-medium">
                                        Inspect
                                    </a>
                                    <form action="<?= site_url('admin/users/' . $u['id'] . '/toggle-status') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium">
                                            <?= ($u['status'] === 'active') ? 'Deactivate' : 'Activate' ?>
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
            <p class="text-sm font-semibold text-slate-300">No Clients Found</p>
            <p class="text-xs text-slate-500 mt-1">Try another search term or register a test user account.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
