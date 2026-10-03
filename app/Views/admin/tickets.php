<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Customer Support Desk</h1>
            <p class="text-xs text-slate-400 mt-1">Review open customer queries, order assistance requests, and dispatch staff responses.</p>
        </div>
    </div>

    <?php if (!empty($tickets)): ?>
        <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Ticket ID</th>
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Priority</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 font-mono text-right">Last Updated</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($tickets as $t): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-400">#<?= esc($t['id']) ?></td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-white block"><?= esc($t['user_name']) ?></span>
                                    <span class="text-[10px] text-slate-500 font-mono"><?= esc($t['user_email']) ?></span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-200 max-w-sm truncate">
                                    <?= esc($t['subject']) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-slate-400 font-semibold uppercase text-[11px]"><?= esc($t['priority']) ?></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php 
                                        $statusClass = match($t['status']) {
                                            'open' => 'text-emerald-400',
                                            'pending' => 'text-blue-400',
                                            default => 'text-slate-500',
                                        };
                                    ?>
                                    <span class="font-bold uppercase text-[11px] <?= $statusClass ?>">
                                        <?= esc($t['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-500 text-[11px]">
                                    <?= !empty($t['last_reply_at']) ? date('M d, H:i', strtotime($t['last_reply_at'])) : date('M d, H:i', strtotime($t['created_at'])) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= site_url('admin/tickets/' . $t['id']) ?>" class="px-2.5 py-1 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-semibold">
                                        Answer Thread
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
            <p class="text-sm font-semibold text-slate-300">Helpdesk Queue Empty</p>
            <p class="text-xs text-slate-500 mt-1">There are no open or pending customer tickets at this time.</p>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
