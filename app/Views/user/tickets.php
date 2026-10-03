<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Support Tickets</h1>
            <p class="text-xs text-slate-400 mt-1">Submit inquiries or request order assistance from our staff.</p>
        </div>
        <a href="<?= site_url('tickets/create') ?>" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
            + Open Ticket
        </a>
    </div>

    <?php if (!empty($tickets)): ?>
        <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Ticket ID</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Priority</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 font-mono text-right">Last Activity</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($tickets as $t): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-500">#<?= esc($t['id']) ?></td>
                                <td class="py-3.5 px-4 font-medium text-white max-w-sm truncate">
                                    <?= esc($t['subject']) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-slate-400 font-medium capitalize"><?= esc($t['priority']) ?></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php 
                                        $statusClass = match($t['status']) {
                                            'open' => 'text-emerald-400',
                                            'pending' => 'text-blue-400',
                                            default => 'text-slate-500',
                                        };
                                    ?>
                                    <span class="font-semibold <?= $statusClass ?> capitalize">
                                        <?= esc($t['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-right text-slate-500 text-[11px]">
                                    <?= !empty($t['last_reply_at']) ? date('M d, H:i', strtotime($t['last_reply_at'])) : date('M d, H:i', strtotime($t['created_at'])) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= site_url('tickets/' . $t['id']) ?>" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs">
                                        View Thread
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#090E1F]">
            <p class="text-sm font-semibold text-slate-300">No Support Tickets</p>
            <p class="text-xs text-slate-500 mt-1">If you have any questions or require order support, click below.</p>
            <div class="mt-4">
                <a href="<?= site_url('tickets/create') ?>" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold inline-block">
                    Open Your First Ticket
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
