<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Ticket #<?= esc($ticket['id']) ?>: <?= esc($ticket['subject']) ?></h1>
            <p class="text-xs text-slate-400 mt-1">Client: <?= esc($ticket['user_name']) ?> (<?= esc($ticket['user_email']) ?>)</p>
        </div>
        <a href="<?= site_url('admin/tickets') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Tickets
        </a>
    </div>

    <!-- Ticket Status Controls -->
    <div class="p-4 rounded-2xl bg-[#080D1D] border border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400">Current Status:</span>
            <span class="font-bold text-xs uppercase text-blue-400"><?= esc($ticket['status']) ?></span>
        </div>

        <form action="<?= site_url('admin/tickets/' . $ticket['id'] . '/status') ?>" method="POST" class="flex items-center gap-2">
            <?= csrf_field() ?>
            <select name="status" class="bg-[#050914] border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-blue-500">
                <option value="open" <?= ($ticket['status'] === 'open') ? 'selected' : '' ?>>Open</option>
                <option value="pending" <?= ($ticket['status'] === 'pending') ? 'selected' : '' ?>>Pending Customer Reply</option>
                <option value="closed" <?= ($ticket['status'] === 'closed') ? 'selected' : '' ?>>Closed / Resolved</option>
            </select>
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold">
                Set Status
            </button>
        </form>
    </div>

    <!-- Thread Messages -->
    <div class="space-y-4">
        <?php foreach ($messages as $msg): ?>
            <?php $isAdmin = ($msg['sender_type'] === 'admin'); ?>
            <div class="rounded-2xl p-5 border <?= $isAdmin ? 'bg-[#0A122E] border-blue-500/30' : 'bg-[#080D1D] border-slate-800' ?>">
                <div class="flex items-center justify-between border-b <?= $isAdmin ? 'border-blue-500/20' : 'border-slate-800' ?> pb-2.5 mb-3 text-xs">
                    <span class="font-bold <?= $isAdmin ? 'text-blue-400' : 'text-white' ?>">
                        <?= $isAdmin ? 'Staff Agent' : esc($ticket['user_name']) ?>
                    </span>
                    <span class="text-[10px] text-slate-500 font-mono">
                        <?= date('M d, Y H:i:s', strtotime($msg['created_at'])) ?>
                    </span>
                </div>
                <div class="text-xs text-slate-300 whitespace-pre-line leading-relaxed">
                    <?= esc($msg['message']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Admin Reply Form -->
    <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 p-6 space-y-3">
        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Dispatch Staff Response</h3>
        <form action="<?= site_url('admin/tickets/' . $ticket['id'] . '/reply') ?>" method="POST" class="space-y-3">
            <?= csrf_field() ?>
            <textarea name="message" required rows="4" placeholder="Enter formal resolution or inquiry update for customer..." class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 leading-relaxed"></textarea>
            <div class="text-right">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                    Send Response to Client
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
