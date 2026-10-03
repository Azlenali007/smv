<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto py-4 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Ticket #<?= esc($ticket['id']) ?>: <?= esc($ticket['subject']) ?></h1>
            <p class="text-xs text-slate-400 mt-1">Status: <span class="capitalize font-semibold text-blue-400"><?= esc($ticket['status']) ?></span> · Priority: <span class="capitalize text-slate-300"><?= esc($ticket['priority']) ?></span></p>
        </div>
        <a href="<?= site_url('tickets') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Tickets
        </a>
    </div>

    <!-- Messages Thread -->
    <div class="space-y-4">
        <?php foreach ($messages as $msg): ?>
            <?php $isAdmin = ($msg['sender_type'] === 'admin'); ?>
            <div class="rounded-2xl p-5 border <?= $isAdmin ? 'bg-[#0B1533] border-blue-500/30' : 'bg-[#090E1F] border-slate-800' ?>">
                <div class="flex items-center justify-between border-b <?= $isAdmin ? 'border-blue-500/20' : 'border-slate-800' ?> pb-2.5 mb-3 text-xs">
                    <span class="font-bold <?= $isAdmin ? 'text-blue-400' : 'text-slate-200' ?>">
                        <?= $isAdmin ? 'Support Team Staff' : esc($user_session['user_name']) ?>
                    </span>
                    <span class="text-[10px] text-slate-500 font-mono">
                        <?= date('M d, Y H:i', strtotime($msg['created_at'])) ?>
                    </span>
                </div>
                <div class="text-xs text-slate-300 whitespace-pre-line leading-relaxed">
                    <?= esc($msg['message']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Reply Box (if not closed) -->
    <?php if ($ticket['status'] !== 'closed'): ?>
        <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Send Reply</h3>
            <form action="<?= site_url('tickets/' . $ticket['id'] . '/reply') ?>" method="POST" class="space-y-3">
                <?= csrf_field() ?>
                <textarea name="message" required rows="4" placeholder="Write your response here..." class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-blue-500"></textarea>
                <div class="text-right">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                        Dispatch Reply
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="p-4 rounded-xl bg-[#060914] border border-slate-800 text-center text-xs text-slate-500">
            This ticket has been marked as closed. Open a new ticket if further assistance is needed.
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
