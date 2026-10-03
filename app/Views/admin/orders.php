<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">System Order Registry</h1>
            <p class="text-xs text-slate-400 mt-1">Audit customer orders, upstream provider dispatch states, and manual overrides.</p>
        </div>

        <form method="GET" action="<?= site_url('admin/orders') ?>" class="flex gap-2">
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search Order ID, Link, or Email..." class="bg-[#080D1D] border border-slate-800 rounded-xl px-3.5 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold">Search</button>
            <?php if ($search || $status): ?>
                <a href="<?= site_url('admin/orders') ?>" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-xs flex items-center">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-1 p-1 bg-[#080D1D] border border-slate-800 rounded-xl overflow-x-auto text-xs">
        <a href="<?= site_url('admin/orders') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= empty($status) ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
            All Orders
        </a>
        <a href="<?= site_url('admin/orders?status=pending') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'pending') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
            Pending
        </a>
        <a href="<?= site_url('admin/orders?status=processing') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'processing') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
            Processing
        </a>
        <a href="<?= site_url('admin/orders?status=completed') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'completed') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
            Completed
        </a>
        <a href="<?= site_url('admin/orders?status=cancelled') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'cancelled') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
            Cancelled
        </a>
        <a href="<?= site_url('admin/orders?status=refunded') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'refunded') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
            Refunded
        </a>
    </div>

    <!-- Orders Table -->
    <?php if (!empty($orders)): ?>
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Order ID</th>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4">Provider / Remote ID</th>
                            <th class="py-3 px-4 font-mono text-right">Quantity</th>
                            <th class="py-3 px-4 font-mono text-right">Charge (INR)</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($orders as $o): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-400">#<?= esc($o['id']) ?></td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-white block"><?= esc($o['user_name'] ?? 'User') ?></span>
                                    <span class="text-[10px] text-slate-500 font-mono"><?= esc($o['user_email'] ?? '') ?></span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs truncate font-medium text-slate-200">
                                    <?= esc($o['service_name']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">
                                    <?php if (!empty($o['provider_name'])): ?>
                                        <span class="text-blue-400"><?= esc($o['provider_name']) ?></span>
                                        <span class="text-slate-500 block text-[10px]"><?= esc($o['provider_order_id'] ?? 'Not Dispatched') ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-600">Manual Service</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right tabular-nums text-slate-300">
                                    <?= number_format($o['quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right tabular-nums font-bold text-blue-400">
                                    ₹<?= number_format((float)$o['charge'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php 
                                        $statusClass = match($o['status']) {
                                            'completed' => 'text-emerald-400',
                                            'processing' => 'text-blue-400',
                                            'cancelled', 'refunded' => 'text-rose-400',
                                            default => 'text-amber-400',
                                        };
                                    ?>
                                    <span class="font-bold uppercase text-[11px] <?= $statusClass ?>">
                                        <?= esc($o['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="<?= site_url('admin/orders/' . $o['id']) ?>" class="px-2.5 py-1 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-semibold">
                                        Inspect
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#080D1D]">
            <p class="text-sm font-semibold text-slate-300">No Orders in Registry</p>
            <p class="text-xs text-slate-500 mt-1">Orders placed by customers will be recorded here.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
