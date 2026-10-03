<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">System Operations Console</h1>
        <p class="text-xs text-slate-400 mt-1">Direct MySQL aggregations across user accounts, provider orders, and verified revenue.</p>
    </div>

    <!-- Admin Metric Cards (Strict Real Data) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Platform Revenue -->
        <div class="p-5 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-1">
            <span class="text-[11px] text-slate-400 font-medium">Platform Revenue (INR Base)</span>
            <div class="text-xl sm:text-2xl font-extrabold text-blue-400 font-mono tabular-nums">
                ₹<?= number_format((float)($stats['total_revenue'] ?? 0), 2) ?>
            </div>
            <p class="text-[10px] text-slate-500 font-mono">Net non-cancelled volume</p>
        </div>

        <!-- Total Registered Users -->
        <div class="p-5 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-1">
            <span class="text-[11px] text-slate-400 font-medium">Registered Clients</span>
            <div class="text-xl sm:text-2xl font-extrabold text-white font-mono tabular-nums">
                <?= number_format($stats['total_users'] ?? 0) ?>
            </div>
            <p class="text-[10px] text-slate-500">Active customer accounts</p>
        </div>

        <!-- Total Orders Placed -->
        <div class="p-5 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-1">
            <span class="text-[11px] text-slate-400 font-medium">Total Orders</span>
            <div class="text-xl sm:text-2xl font-extrabold text-white font-mono tabular-nums">
                <?= number_format($stats['total_orders'] ?? 0) ?>
            </div>
            <p class="text-[10px] text-slate-500">All database order records</p>
        </div>

        <!-- Pending / In Progress Orders -->
        <div class="p-5 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-1">
            <span class="text-[11px] text-slate-400 font-medium">Active Pipeline Orders</span>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-400 font-mono tabular-nums">
                <?= number_format($stats['pending_orders'] ?? 0) ?>
            </div>
            <p class="text-[10px] text-slate-500">Pending &amp; processing</p>
        </div>

    </div>

    <!-- Secondary Counters -->
    <div class="grid grid-cols-2 gap-4">
        <div class="p-4 rounded-xl bg-[#060A17] border border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-400 font-medium">Completed Fulfillments</span>
            <span class="text-sm font-bold text-emerald-400 font-mono tabular-nums"><?= number_format($stats['completed_orders'] ?? 0) ?></span>
        </div>
        <div class="p-4 rounded-xl bg-[#060A17] border border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-400 font-medium">Cancelled / Refunded</span>
            <span class="text-sm font-bold text-rose-400 font-mono tabular-nums"><?= number_format($stats['cancelled_orders'] ?? 0) ?></span>
        </div>
    </div>

    <!-- Dual Stream: Recent Users & Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Orders -->
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-xs font-bold text-white uppercase tracking-wider">Recent Orders</h2>
                <a href="<?= site_url('admin/orders') ?>" class="text-[11px] text-blue-400 hover:text-blue-300">View All Orders &rarr;</a>
            </div>
            <?php if (!empty($recentOrders)): ?>
                <div class="divide-y divide-slate-800/60 text-xs">
                    <?php foreach ($recentOrders as $ro): ?>
                        <div class="p-3.5 hover:bg-slate-800/20 flex items-center justify-between">
                            <div class="max-w-xs truncate">
                                <span class="font-bold text-white">#<?= esc($ro['id']) ?> · <?= esc($ro['user_name'] ?? 'User') ?></span>
                                <span class="text-[11px] text-slate-400 block truncate"><?= esc($ro['service_name']) ?></span>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-blue-400 block">₹<?= number_format((float)$ro['charge'], 2) ?></span>
                                <span class="text-[10px] font-semibold text-slate-400 uppercase"><?= esc($ro['status']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-xs text-slate-500">No orders recorded in database.</div>
            <?php endif; ?>
        </div>

        <!-- Recent Users -->
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-xs font-bold text-white uppercase tracking-wider">Recent Registered Clients</h2>
                <a href="<?= site_url('admin/users') ?>" class="text-[11px] text-blue-400 hover:text-blue-300">Manage Users &rarr;</a>
            </div>
            <?php if (!empty($recentUsers)): ?>
                <div class="divide-y divide-slate-800/60 text-xs">
                    <?php foreach ($recentUsers as $ru): ?>
                        <div class="p-3.5 hover:bg-slate-800/20 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-white block"><?= esc($ru['name']) ?></span>
                                <span class="text-[11px] text-slate-400 font-mono"><?= esc($ru['email']) ?></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-semibold <?= ($ru['status'] === 'active') ? 'text-emerald-400' : 'text-rose-400' ?> uppercase block">
                                    <?= esc($ru['status']) ?>
                                </span>
                                <span class="text-[10px] text-slate-500 font-mono"><?= date('M d, Y', strtotime($ru['created_at'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-xs text-slate-500">No users registered yet.</div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Recent Payment Activity -->
    <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-xs font-bold text-white uppercase tracking-wider">Payment Transaction Activity</h2>
            <a href="<?= site_url('admin/payments') ?>" class="text-[11px] text-blue-400 hover:text-blue-300">Full Payment Ledger &rarr;</a>
        </div>
        <?php if (!empty($recentPayments)): ?>
            <div class="divide-y divide-slate-800/60 text-xs">
                <?php foreach ($recentPayments as $rp): ?>
                    <div class="p-3.5 hover:bg-slate-800/20 flex items-center justify-between">
                        <div>
                            <span class="font-mono text-slate-400"><?= esc($rp['transaction_reference']) ?></span>
                            <span class="text-slate-300 ml-2 font-medium"><?= esc($rp['user_name'] ?? 'User') ?></span>
                        </div>
                        <div class="flex items-center gap-4 text-right">
                            <span class="font-mono font-bold text-white">
                                <?= esc($rp['currency']) ?> <?= number_format((float)$rp['amount'], 2) ?>
                                <span class="text-[10px] text-slate-500">(₹<?= number_format((float)$rp['amount_in_inr'], 2) ?> INR)</span>
                            </span>
                            <span class="text-[10px] font-semibold uppercase <?= ($rp['status'] === 'completed') ? 'text-emerald-400' : 'text-amber-400' ?>">
                                <?= esc($rp['status']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-8 text-center text-xs text-slate-500">No payment transaction activity found.</div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
