<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Payment Audit Ledger</h1>
            <p class="text-xs text-slate-400 mt-1">Audit trail of all initiated, completed, or failed payment gateway invoices.</p>
        </div>
        <a href="<?= site_url('admin/payments') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Gateways
        </a>
    </div>

    <?php if (!empty($transactions)): ?>
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Ref ID</th>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Gateway</th>
                            <th class="py-3 px-4 font-mono text-right">Gateway Amount</th>
                            <th class="py-3 px-4 font-mono text-right">INR Credit Base</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 font-mono text-right">Initiated At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3 px-4 font-mono text-slate-400"><?= esc($tx['transaction_reference']) ?></td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-white block"><?= esc($tx['user_name'] ?? 'User') ?></span>
                                    <span class="text-[10px] text-slate-500 font-mono"><?= esc($tx['user_email'] ?? '') ?></span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-200"><?= esc($tx['gateway_name']) ?></td>
                                <td class="py-3 px-4 font-mono text-right font-medium text-white tabular-nums">
                                    <?= esc($tx['currency']) ?> <?= number_format((float)$tx['amount'], 2) ?>
                                </td>
                                <td class="py-3 px-4 font-mono text-right font-bold text-blue-400 tabular-nums">
                                    ₹<?= number_format((float)$tx['amount_in_inr'], 2) ?> INR
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-xs font-semibold uppercase <?= ($tx['status'] === 'completed') ? 'text-emerald-400' : 'text-amber-400' ?>">
                                        <?= esc($tx['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-right text-slate-500 text-[11px]"><?= date('Y-m-d H:i', strtotime($tx['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#080D1D]">
            <p class="text-sm font-semibold text-slate-300">No Payment Records Found</p>
            <p class="text-xs text-slate-500 mt-1">When users initiate deposits through payment gateways, invoices appear here.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
