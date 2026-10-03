<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Wallet Ledger</h1>
            <p class="text-xs text-slate-400 mt-1">Immutable record of every balance change on your account.</p>
        </div>
        <a href="<?= site_url('add-funds') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Wallet
        </a>
    </div>

    <?php if (!empty($transactions)): ?>
        <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">TX ID</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Remarks / Purpose</th>
                            <th class="py-3 px-4 font-mono text-right">Opening</th>
                            <th class="py-3 px-4 font-mono text-right">Amount</th>
                            <th class="py-3 px-4 font-mono text-right">Closing</th>
                            <th class="py-3 px-4 font-mono text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3 px-4 font-mono text-slate-500">#<?= esc($tx['id']) ?></td>
                                <td class="py-3 px-4 font-medium text-white"><?= ucfirst(str_replace('_', ' ', esc($tx['type']))) ?></td>
                                <td class="py-3 px-4 text-slate-300 max-w-xs truncate"><?= esc($tx['remarks']) ?></td>
                                <td class="py-3 px-4 font-mono text-right text-slate-400 tabular-nums">₹<?= number_format((float)$tx['opening_balance'], 2) ?></td>
                                <?php $isCredit = in_array($tx['type'], ['deposit', 'refund']) || ((float)$tx['amount'] > 0 && $tx['type'] === 'manual_adjustment'); ?>
                                <td class="py-3 px-4 font-mono text-right font-bold tabular-nums <?= $isCredit ? 'text-emerald-400' : 'text-slate-200' ?>">
                                    <?= $isCredit ? '+' : '-' ?>₹<?= number_format(abs((float)$tx['amount']), 2) ?>
                                </td>
                                <td class="py-3 px-4 font-mono text-right text-white font-semibold tabular-nums">₹<?= number_format((float)$tx['closing_balance'], 2) ?></td>
                                <td class="py-3 px-4 font-mono text-right text-slate-500 text-[11px]"><?= date('Y-m-d H:i:s', strtotime($tx['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#090E1F]">
            <p class="text-sm font-semibold text-slate-300">No Ledger Records</p>
            <p class="text-xs text-slate-500 mt-1">Balance movements will automatically appear here.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
