<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header Greeting & Quick Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                Account Overview
            </h1>
            <p class="text-xs text-slate-400 mt-1">Real-time balances and ledger metrics recorded in MySQL.</p>
        </div>
        <div>
            <a href="<?= site_url('new-order') ?>" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Place New Order
            </a>
        </div>
    </div>

    <!-- Real Metrics Grid (No fake percentages, no fake arrows) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Wallet Balance Card (Converted from INR base into display currency) -->
        <div class="p-5 rounded-2xl bg-[#090E1F] border border-slate-800/80 space-y-2">
            <span class="text-[11px] font-medium text-slate-400">Available Balance</span>
            <div class="text-xl sm:text-2xl font-extrabold text-white font-mono tabular-nums tracking-tight">
                <?= $currency_service->format($currency_service->convertFromInr($wallet['balance'] ?? 0, $current_currency), $current_currency) ?>
            </div>
            <p class="text-[10px] text-slate-500 font-mono">
                Base: ₹<?= number_format((float)($wallet['balance'] ?? 0), 2) ?> INR
            </p>
        </div>

        <!-- Total Spend -->
        <div class="p-5 rounded-2xl bg-[#090E1F] border border-slate-800/80 space-y-2">
            <span class="text-[11px] font-medium text-slate-400">Total Spend</span>
            <div class="text-xl sm:text-2xl font-extrabold text-white font-mono tabular-nums tracking-tight">
                <?= $currency_service->format($currency_service->convertFromInr($stats['total_spend'] ?? 0, $current_currency), $current_currency) ?>
            </div>
            <p class="text-[10px] text-slate-500 font-mono">
                Base: ₹<?= number_format((float)($stats['total_spend'] ?? 0), 2) ?> INR
            </p>
        </div>

        <!-- Total Orders -->
        <div class="p-5 rounded-2xl bg-[#090E1F] border border-slate-800/80 space-y-2">
            <span class="text-[11px] font-medium text-slate-400">Total Orders</span>
            <div class="text-xl sm:text-2xl font-extrabold text-white font-mono tabular-nums tracking-tight">
                <?= number_format($stats['total_orders'] ?? 0) ?>
            </div>
            <p class="text-[10px] text-slate-500">All submitted orders</p>
        </div>

        <!-- Pending / Processing Orders -->
        <div class="p-5 rounded-2xl bg-[#090E1F] border border-slate-800/80 space-y-2">
            <span class="text-[11px] font-medium text-slate-400">Active / In Progress</span>
            <div class="text-xl sm:text-2xl font-extrabold text-blue-400 font-mono tabular-nums tracking-tight">
                <?= number_format($stats['pending_orders'] ?? 0) ?>
            </div>
            <p class="text-[10px] text-slate-500">Pending and processing</p>
        </div>

    </div>

    <!-- Secondary Status Counts -->
    <div class="grid grid-cols-2 gap-4">
        <div class="p-4 rounded-xl bg-[#070C1B] border border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-400">Completed Orders</span>
            <span class="text-sm font-bold text-emerald-400 font-mono tabular-nums"><?= number_format($stats['completed_orders'] ?? 0) ?></span>
        </div>
        <div class="p-4 rounded-xl bg-[#070C1B] border border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-400">Cancelled / Refunded</span>
            <span class="text-sm font-bold text-rose-400 font-mono tabular-nums"><?= number_format($stats['cancelled_orders'] ?? 0) ?></span>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-white tracking-tight">Recent Orders</h2>
            <a href="<?= site_url('orders') ?>" class="text-xs text-blue-400 hover:text-blue-300 font-semibold">
                View All &rarr;
            </a>
        </div>

        <?php if (!empty($recentOrders)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Order ID</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4 font-mono text-right">Quantity</th>
                            <th class="py-3 px-4 font-mono text-right">Charge</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 font-mono text-right">Date</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($recentOrders as $order): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-400">#<?= esc($order['id']) ?></td>
                                <td class="py-3.5 px-4 font-medium text-white max-w-xs truncate">
                                    <?= esc($order['service_name'] ?? ('Service #' . $order['service_id'])) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right tabular-nums text-slate-300">
                                    <?= number_format($order['quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right tabular-nums text-blue-400 font-semibold">
                                    <?= $currency_service->format($currency_service->convertFromInr($order['charge'], $current_currency), $current_currency) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php 
                                        $statusClass = match($order['status']) {
                                            'completed' => 'text-emerald-400',
                                            'processing' => 'text-blue-400',
                                            'cancelled', 'refunded' => 'text-rose-400',
                                            default => 'text-amber-400',
                                        };
                                    ?>
                                    <span class="font-medium text-xs <?= $statusClass ?>">
                                        <?= ucfirst(esc($order['status'])) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-500 text-[11px]">
                                    <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= site_url('orders/' . $order['id']) ?>" class="text-xs text-blue-400 hover:text-blue-300 font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Strict Empty State -->
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-800/60 border border-slate-700 mx-auto flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-white">No Orders Placed Yet</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">You have not submitted any orders yet. Place your first order to get started.</p>
                <div class="mt-4">
                    <a href="<?= site_url('new-order') ?>" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold inline-block">
                        Place First Order
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
