<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Order Management</h1>
            <p class="text-xs text-slate-400 mt-1">Review live execution states for all orders placed on your account.</p>
        </div>
        <a href="<?= site_url('new-order') ?>" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
            + New Order
        </a>
    </div>

    <!-- Status Tabs & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <!-- Status Tabs (Segmented control) -->
        <div class="flex items-center gap-1 p-1 bg-[#090E1F] border border-slate-800 rounded-xl overflow-x-auto text-xs">
            <a href="<?= site_url('orders') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= empty($status) ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
                All Orders
            </a>
            <a href="<?= site_url('orders?status=pending') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'pending') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
                Pending
            </a>
            <a href="<?= site_url('orders?status=processing') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'processing') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
                Processing
            </a>
            <a href="<?= site_url('orders?status=completed') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'completed') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
                Completed
            </a>
            <a href="<?= site_url('orders?status=cancelled') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'cancelled') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
                Cancelled
            </a>
            <a href="<?= site_url('orders?status=refunded') ?>" class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors <?= ($status === 'refunded') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400 hover:text-white' ?>">
                Refunded
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="<?= site_url('orders') ?>" class="flex gap-2">
            <?php if (!empty($status)): ?>
                <input type="hidden" name="status" value="<?= esc($status) ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search Order ID or link..." class="bg-[#090E1F] border border-slate-800 rounded-xl px-3.5 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium">
                Search
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <?php if (!empty($orders)): ?>
        <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Order ID</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4">Target Link</th>
                            <th class="py-3 px-4 font-mono text-right">Quantity</th>
                            <th class="py-3 px-4 font-mono text-right">Charge</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 font-mono text-right">Date</th>
                            <th class="py-3 px-4 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($orders as $o): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-400">#<?= esc($o['id']) ?></td>
                                <td class="py-3.5 px-4 font-medium text-white max-w-xs truncate">
                                    <?= esc($o['service_name'] ?? ('Service #' . $o['service_id'])) ?>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs truncate text-blue-400">
                                    <a href="<?= esc($o['link']) ?>" target="_blank" rel="noopener noreferrer" class="hover:underline">
                                        <?= esc($o['link']) ?>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right tabular-nums text-slate-300">
                                    <?= number_format($o['quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right tabular-nums text-blue-400 font-semibold">
                                    <?= $currency_service->format($currency_service->convertFromInr($o['charge'], $current_currency), $current_currency) ?>
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
                                    <span class="font-semibold text-xs <?= $statusClass ?>">
                                        <?= ucfirst(esc($o['status'])) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-500 text-[11px]">
                                    <?= date('M d, Y H:i', strtotime($o['created_at'])) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= site_url('orders/' . $o['id']) ?>" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <?php if ($totalPages > 1): ?>
                <div class="p-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <div>
                        Showing Page <?= esc($currentPage) ?> of <?= esc($totalPages) ?> (Total: <?= number_format($totalOrders) ?> orders)
                    </div>
                    <div class="flex gap-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= site_url('orders?page=' . ($currentPage - 1) . ($status ? '&status=' . $status : '') . ($search ? '&search=' . $search : '')) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200">
                                Previous
                            </a>
                        <?php endif; ?>
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= site_url('orders?page=' . ($currentPage + 1) . ($status ? '&status=' . $status : '') . ($search ? '&search=' . $search : '')) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#090E1F]">
            <p class="text-sm font-semibold text-slate-300">No Orders Located</p>
            <p class="text-xs text-slate-500 mt-1">There are no orders matching your selected status filter or search parameters.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
