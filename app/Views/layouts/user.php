<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'User Panel | ' . $site_name) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/resources/css/app.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #060913; color: #F1F5F9; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-[#060913] text-slate-100 antialiased selection:bg-blue-600 selection:text-white" x-data="{ sidebarOpen: false }">

    <!-- Mobile Navigation Drawer Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm lg:hidden transition-opacity"></div>

    <!-- User Sidebar Navigation -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0A0F1F] border-r border-slate-800/80 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0">
        
        <!-- Brand Wordmark -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800/80">
            <a href="<?= site_url('dashboard') ?>" class="flex items-center gap-2.5 font-bold text-white tracking-tight">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-base"><?= esc($site_name) ?></span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <?php $currentUri = service('uri')->getPath(); ?>
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="<?= site_url('dashboard') ?>" class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all <?= ($currentUri === 'dashboard' || $currentUri === '') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="<?= site_url('new-order') ?>" class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all <?= str_contains($currentUri, 'new-order') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>New Order</span>
            </a>

            <a href="<?= site_url('orders') ?>" class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all <?= (str_contains($currentUri, 'orders') && !str_contains($currentUri, 'new-order')) ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Orders</span>
            </a>

            <a href="<?= site_url('user-services') ?>" class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all <?= str_contains($currentUri, 'user-services') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span>Services</span>
            </a>

            <a href="<?= site_url('add-funds') ?>" class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all <?= str_contains($currentUri, 'add-funds') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Add Funds</span>
            </a>

            <a href="<?= site_url('tickets') ?>" class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all <?= str_contains($currentUri, 'tickets') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Tickets</span>
            </a>

            <a href="<?= site_url('profile') ?>" class="flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl transition-all <?= str_contains($currentUri, 'profile') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profile</span>
            </a>
        </nav>

        <!-- User Quick Card & Logout -->
        <div class="p-3 border-t border-slate-800/80 bg-[#070B16]">
            <div class="flex items-center justify-between p-2">
                <div class="truncate mr-2">
                    <p class="text-xs font-medium text-white truncate"><?= esc($user_session['user_name'] ?? 'User') ?></p>
                    <p class="text-[11px] text-slate-500 truncate"><?= esc($user_session['user_email'] ?? '') ?></p>
                </div>
                <a href="<?= site_url('logout') ?>" title="Logout" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Workspace Area -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        
        <!-- Top Workspace Bar -->
        <header class="h-16 bg-[#080D1C]/90 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-400 hover:text-white rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="hidden sm:block text-xs text-slate-400 font-medium">
                    ApexPulse Enterprise SMM Node
                </div>
            </div>

            <!-- Currency Selector & Action Header -->
            <div class="flex items-center gap-3">
                
                <!-- Display Currency Switcher -->
                <form action="<?= site_url('profile/set-currency') ?>" method="POST" class="flex items-center gap-1.5 text-xs">
                    <?= csrf_field() ?>
                    <label class="hidden md:inline text-slate-500 font-medium">Currency:</label>
                    <select name="currency" onchange="this.form.submit()" class="bg-[#0B1124] border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-slate-200 focus:outline-none focus:border-blue-500 cursor-pointer font-mono">
                        <?php if (!empty($currencies)): ?>
                            <?php foreach ($currencies as $c): ?>
                                <option value="<?= esc($c['code']) ?>" <?= ($c['code'] === $current_currency) ? 'selected' : '' ?>>
                                    <?= esc($c['code']) ?> (<?= esc($c['symbol']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="INR" selected>INR (₹)</option>
                        <?php endif; ?>
                    </select>
                </form>

                <a href="<?= site_url('add-funds') ?>" class="px-3 py-1.5 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 border border-blue-500/30 text-xs font-semibold transition-all">
                    + Add Funds
                </a>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="p-3 mb-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between">
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-3 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs flex items-center justify-between">
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="p-3 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <p><?= esc($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- View Body -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

</body>
</html>
