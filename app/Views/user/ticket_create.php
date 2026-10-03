<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto py-4 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Open New Ticket</h1>
            <p class="text-xs text-slate-400 mt-1">Our support staff usually responds within a few hours.</p>
        </div>
        <a href="<?= site_url('tickets') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Tickets
        </a>
    </div>

    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6 sm:p-8">
        <form action="<?= site_url('tickets/store') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Subject</label>
                <input type="text" name="subject" required placeholder="Brief description of your issue or order question" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Priority</label>
                <select name="priority" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Message Details</label>
                <textarea name="message" required rows="5" placeholder="Include relevant order IDs, target URLs, or transaction reference numbers..." class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-blue-500 leading-relaxed"></textarea>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.35)]">
                Submit Support Ticket
            </button>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
