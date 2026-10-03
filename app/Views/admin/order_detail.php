<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Order #<?= esc($order['id']) ?> Operations</h1>
            <p class="text-xs text-slate-400 mt-1">Placed by <?= esc($order['user_name']) ?> (<?= esc($order['user_email']) ?>)</p>
        </div>
        <a href="<?= site_url('admin/orders') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Orders
        </a>
    </div>

    <!-- Order Specs Card -->
    <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 p-6 sm:p-8 space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-5 gap-3">
            <div>
                <span class="text-[11px] text-slate-500 font-medium block">Lifecycle Status</span>
                <?php 
                    $statusClass = match($order['status']) {
                        'completed' => 'text-emerald-400',
                        'processing' => 'text-blue-400',
                        'cancelled', 'refunded' => 'text-rose-400',
                        default => 'text-amber-400',
                    };
                ?>
                <span class="text-lg font-bold <?= $statusClass ?> uppercase tracking-wider">
                    <?= esc($order['status']) ?>
                </span>
            </div>

            <!-- Status Override Form -->
            <form action="<?= site_url('admin/orders/' . $order['id'] . '/status') ?>" method="POST" class="flex items-center gap-2">
                <?= csrf_field() ?>
                <select name="status" class="bg-[#050914] border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="pending" <?= ($order['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= ($order['status'] === 'processing') ? 'selected' : '' ?>>Processing</option>
                    <option value="completed" <?= ($order['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= ($order['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled (Refunds Wallet)</option>
                    <option value="refunded" <?= ($order['status'] === 'refunded') ? 'selected' : '' ?>>Refunded (Refunds Wallet)</option>
                </select>
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                    Update Status
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs">
            <div>
                <span class="text-slate-500 block mb-1">Service</span>
                <span class="text-sm font-semibold text-white block"><?= esc($order['service_name']) ?></span>
            </div>

            <div>
                <span class="text-slate-500 block mb-1">Base Charge (INR)</span>
                <span class="text-sm font-bold text-blue-400 font-mono tabular-nums block">₹<?= number_format((float)$order['charge'], 4) ?> INR</span>
            </div>

            <div class="sm:col-span-2">
                <span class="text-slate-500 block mb-1">Target Link</span>
                <a href="<?= esc($order['link']) ?>" target="_blank" class="text-blue-400 hover:underline break-all font-mono">
                    <?= esc($order['link']) ?>
                </a>
            </div>

            <div>
                <span class="text-slate-500 block mb-1">Quantity</span>
                <span class="text-sm font-semibold text-white font-mono tabular-nums"><?= number_format($order['quantity']) ?> units</span>
            </div>

            <div>
                <span class="text-slate-500 block mb-1">Created At</span>
                <span class="text-xs text-slate-300 font-mono"><?= date('Y-m-d H:i:s', strtotime($order['created_at'])) ?></span>
            </div>
        </div>

        <!-- Provider API Diagnostics Section -->
        <div class="pt-5 border-t border-slate-800 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Provider API Dispatch Diagnostics</h3>
                <?php if (!empty($order['provider_id']) && !empty($order['provider_service_id'])): ?>
                    <form action="<?= site_url('admin/orders/' . $order['id'] . '/resend') ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-medium text-blue-400">
                            Re-dispatch to Provider
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3 rounded-xl bg-[#050914] border border-slate-800">
                    <span class="text-slate-500 block text-[11px]">Provider Name</span>
                    <span class="font-bold text-white block mt-0.5"><?= esc($order['provider_name'] ?? 'None (Manual)') ?></span>
                </div>

                <div class="p-3 rounded-xl bg-[#050914] border border-slate-800">
                    <span class="text-slate-500 block text-[11px]">Remote Order ID</span>
                    <span class="font-mono text-blue-400 font-semibold block mt-0.5"><?= esc($order['provider_order_id'] ?? 'Not Assigned') ?></span>
                </div>

                <div class="p-3 rounded-xl bg-[#050914] border border-slate-800">
                    <span class="text-slate-500 block text-[11px]">Provider Status</span>
                    <span class="font-semibold text-slate-200 block mt-0.5"><?= esc($order['provider_status'] ?? 'N/A') ?></span>
                </div>
            </div>

            <?php if (!empty($order['api_response'])): ?>
                <div>
                    <span class="text-[11px] text-slate-500 font-mono block mb-1">Raw API Provider Payload:</span>
                    <pre class="p-3 rounded-xl bg-[#040712] border border-slate-800 font-mono text-[11px] text-slate-400 overflow-x-auto"><?= esc($order['api_response']) ?></pre>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
