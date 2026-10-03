<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Client Inspection: <?= esc($user['name']) ?></h1>
            <p class="text-xs text-slate-400 mt-1">User ID: #<?= esc($user['id']) ?> · Email: <?= esc($user['email']) ?></p>
        </div>
        <a href="<?= site_url('admin/users') ?>" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Users
        </a>
    </div>

    <!-- User Profile & Manual Balance Adjustment Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Account Summary -->
        <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-4">
            <h2 class="text-xs font-bold text-white uppercase tracking-wider">Account Metrics</h2>
            
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-500 block">Current Wallet Balance</span>
                    <span class="text-xl font-bold text-blue-400 font-mono tabular-nums block">
                        ₹<?= number_format((float)($wallet['balance'] ?? 0), 2) ?> INR
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block">Total Spend</span>
                    <span class="text-sm font-semibold text-white font-mono tabular-nums block">
                        ₹<?= number_format((float)($stats['total_spend'] ?? 0), 2) ?> INR
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block">Account Status</span>
                    <span class="font-bold text-xs uppercase <?= ($user['status'] === 'active') ? 'text-emerald-400' : 'text-rose-400' ?>">
                        <?= esc($user['status']) ?>
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 block">Registered At</span>
                    <span class="text-slate-400 font-mono"><?= date('Y-m-d H:i:s', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>

        <!-- Atomic Wallet Adjustment Form -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-4">
            <div>
                <h2 class="text-xs font-bold text-white uppercase tracking-wider">Manual Wallet Balance Adjustment</h2>
                <p class="text-xs text-slate-400 mt-1">Executes an atomic database transaction with admin ID logging and ledger creation.</p>
            </div>

            <form action="<?= site_url('admin/users/' . $user['id'] . '/adjust-wallet') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Action Type</label>
                        <select name="type" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="credit">Credit Balance (+)</option>
                            <option value="debit">Debit Balance (-)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Amount (INR Base)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="100.00" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Audit Reason / Remarks (Required)</label>
                    <input type="text" name="reason" required placeholder="e.g., Manual bonus credit or approved offline wire transfer" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-[0_0_15px_rgba(37,99,235,0.3)]">
                    Execute Atomic Adjustment
                </button>
            </form>
        </div>

    </div>

    <!-- Recent Client Orders & Transactions Tabs -->
    <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 p-6 space-y-4">
        <h2 class="text-xs font-bold text-white uppercase tracking-wider">User's Recent Orders</h2>
        <?php if (!empty($orders)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">Order ID</th>
                            <th class="py-2.5 px-3">Service</th>
                            <th class="py-2.5 px-3 font-mono text-right">Quantity</th>
                            <th class="py-2.5 px-3 font-mono text-right">Charge</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 font-mono text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="py-2.5 px-3 font-mono">#<?= esc($o['id']) ?></td>
                                <td class="py-2.5 px-3 max-w-xs truncate"><?= esc($o['service_name']) ?></td>
                                <td class="py-2.5 px-3 font-mono text-right"><?= number_format($o['quantity']) ?></td>
                                <td class="py-2.5 px-3 font-mono text-right font-bold text-blue-400">₹<?= number_format((float)$o['charge'], 2) ?></td>
                                <td class="py-2.5 px-3 font-semibold uppercase text-[11px]"><?= esc($o['status']) ?></td>
                                <td class="py-2.5 px-3 font-mono text-right text-slate-500"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-xs text-slate-500">No orders placed by this user yet.</p>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
