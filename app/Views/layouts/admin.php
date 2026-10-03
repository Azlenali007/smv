<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Console | ' . $site_name) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/resources/css/app.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #040711; color: #F1F5F9; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-[#040711] text-slate-100 antialiased" x-data="{ adminSidebar: false }">

    <!-- Mobile Drawer Overlay -->
    <div x-show="adminSidebar" x-cloak @click="adminSidebar = false" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm lg:hidden"></div>

    <!-- Admin Sidebar Navigation -->
    <aside :class="adminSidebar ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#080D1D] border-r border-slate-800/80 flex flex-col transition-transform duration-200 lg:translate-x-0">
        
        <!-- Header Wordmark -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800/80">
            <a href="<?= site_url('admin/dashboard') ?>" class="flex items-center gap-2.5 font-bold text-white tracking-tight">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-[0_0_15px_rgba(37,99,235,0.5)]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <span class="block text-sm font-bold text-white"><?= esc($site_name) ?></span>
                    <span class="block text-[10px] text-blue-400 font-mono tracking-wider">ADMIN ROOT</span>
                </div>
            </a>
            <button @click="adminSidebar = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Admin Nav Items -->
        <?php $uri = service('uri')->getPath(); ?>
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            
            <a href="<?= site_url('admin/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= ($uri === 'admin/dashboard' || $uri === 'admin') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="<?= site_url('admin/users') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/users') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Users</span>
            </a>

            <a href="<?= site_url('admin/orders') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/orders') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>Orders</span>
            </a>

            <a href="<?= site_url('admin/categories') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/categories') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span>Categories</span>
            </a>

            <a href="<?= site_url('admin/services') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/services') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Services</span>
            </a>

            <a href="<?= site_url('admin/providers') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/providers') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Providers (API)</span>
            </a>

            <a href="<?= site_url('admin/payments') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/payments') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Payments</span>
            </a>

            <a href="<?= site_url('admin/currencies') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/currencies') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Currencies (INR Base)</span>
            </a>

            <a href="<?= site_url('admin/tickets') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/tickets') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Tickets</span>
            </a>

            <a href="<?= site_url('admin/settings') ?>" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded-xl transition-all <?= str_contains($uri, 'admin/settings') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Settings</span>
            </a>
        </nav>

        <!-- Admin User / Logout -->
        <div class="p-3 border-t border-slate-800/80 bg-[#060A16]">
            <div class="flex items-center justify-between p-2">
                <div class="truncate mr-2">
                    <p class="text-xs font-semibold text-white truncate"><?= esc($user_session['user_name'] ?? 'Admin') ?></p>
                    <p class="text-[10px] text-blue-400 font-mono">SUPERADMIN</p>
                </div>
                <a href="<?= site_url('admin/logout') ?>" title="Sign Out" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Admin Workspace Content -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        
        <!-- Admin Topbar -->
        <header class="h-16 bg-[#060914]/90 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="adminSidebar = true" class="lg:hidden p-2 text-slate-400 hover:text-white rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="text-xs font-medium text-slate-400">
                    Administrator Operations Gateway
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="<?= site_url('dashboard') ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white text-xs font-medium transition-colors">
                    View User Panel &rarr;
                </a>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="p-3 mb-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 text-xs">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-3 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs">
                    <?= esc(session()->getFlashdata('error')) ?>
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

        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

</body>
</html>
