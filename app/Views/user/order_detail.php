<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto py-4 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Order #<?= esc($order['id']) ?> Details</h1>
            <p class="text-xs text-slate-400 mt-1">Order receipt and live status ledger.</p>
        </div>
        <a href="<?= site_url('orders') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Orders
        </a>
    </div>

    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6 sm:p-8 space-y-6">
        
        <!-- Header Status Bar -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-5">
            <div>
                <span class="text-[11px] text-slate-500 font-medium block">Current Lifecycle Status</span>
                <?php 
                    $statusClass = match($order['status']) {
                        'completed' => 'text-emerald-400',
                        'processing' => 'text-blue-400',
                        'cancelled', 'refunded' => 'text-rose-400',
                        default => 'text-amber-400',
                    };
                ?>
                <span class="text-lg font-bold <?= $statusClass ?> uppercase tracking-wide">
                    <?= esc($order['status']) ?>
                </span>
            </div>
            <div class="text-right">
                <span class="text-[11px] text-slate-500 font-medium block">Order Date</span>
                <span class="text-xs text-slate-300 font-mono">
                    <?= date('Y-m-d H:i:s', strtotime($order['created_at'])) ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs">
            <div>
                <span class="text-slate-500 block mb-1">Service</span>
                <span class="text-sm font-semibold text-white block">
                    <?= esc($service['name'] ?? ('Service #' . $order['service_id'])) ?>
                </span>
            </div>

            <div>
                <span class="text-slate-500 block mb-1">Charge Amount</span>
                <span class="text-sm font-bold text-blue-400 font-mono tabular-nums block">
                    <?= $currency_service->format($currency_service->convertFromInr($order['charge'], $current_currency), $current_currency) ?>
                    <span class="text-[10px] text-slate-500">(₹<?= number_format((float)$order['charge'], 2) ?> INR base)</span>
                </span>
            </div>

            <div class="sm:col-span-2">
                <span class="text-slate-500 block mb-1">Target Link</span>
                <a href="<?= esc($order['link']) ?>" target="_blank" class="text-blue-400 hover:underline break-all font-mono">
                    <?= esc($order['link']) ?>
                </a>
            </div>

            <div>
                <span class="text-slate-500 block mb-1">Order Quantity</span>
                <span class="text-sm font-semibold text-white font-mono tabular-nums">
                    <?= number_format($order['quantity']) ?> units
                </span>
            </div>

            <div>
                <span class="text-slate-500 block mb-1">Last System Update</span>
                <span class="text-xs text-slate-400 font-mono">
                    <?= !empty($order['updated_at']) ? date('Y-m-d H:i:s', strtotime($order['updated_at'])) : date('Y-m-d H:i:s', strtotime($order['created_at'])) ?>
                </span>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
