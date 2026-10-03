<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? $site_name) ?></title>
    <meta name="description" content="<?= esc($site_description) ?>">
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Production Compiled Tailwind CSS & Alpine.js -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script defer src="<?= base_url('assets/js/app.js') ?>"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #050811; color: #F8FAFC; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-[#050811] text-slate-100 antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileMenu: false }">

    <!-- Subtle electric ambient glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[350px] bg-gradient-to-b from-blue-600/10 via-blue-500/5 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <!-- Top Navigation Bar (Strict 3-zone contract) -->
    <header class="sticky top-0 z-40 w-full backdrop-blur-xl bg-[#050811]/80 border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Zone 1: Single text element wordmark -->
            <a href="<?= site_url('/') ?>" class="flex items-center gap-2.5 text-lg font-bold tracking-tight text-white group">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-[0_0_15px_rgba(59,130,246,0.4)] group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span><?= esc($site_name) ?></span>
            </a>

            <!-- Zone 2: Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-400">
                <a href="<?= site_url('/') ?>" class="hover:text-white transition-colors">Home</a>
                <a href="<?= site_url('services') ?>" class="hover:text-white transition-colors">Services</a>
                <a href="<?= site_url('faq') ?>" class="hover:text-white transition-colors">FAQ</a>
                <a href="<?= site_url('terms') ?>" class="hover:text-white transition-colors">Terms</a>
            </nav>

            <!-- Zone 3: Primary Actions -->
            <div class="flex items-center gap-3">
                <?php if (!empty($user_session['is_logged_in'])): ?>
                    <a href="<?= site_url('dashboard') ?>" class="px-4 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-[0_0_20px_rgba(37,99,235,0.3)] transition-all">
                        Open Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('login') ?>" class="px-3.5 py-1.5 text-xs font-medium text-slate-300 hover:text-white transition-colors">
                        Sign In
                    </a>
                    <a href="<?= site_url('register') ?>" class="px-4 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-[0_0_20px_rgba(37,99,235,0.35)] transition-all">
                        Get Started
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" type="button" class="md:hidden p-2 text-slate-400 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-cloak/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenu" x-cloak @click.away="mobileMenu = false" class="md:hidden border-b border-slate-800 bg-[#070C1A] px-4 pt-3 pb-5 space-y-3">
            <a href="<?= site_url('/') ?>" class="block py-2 text-sm font-medium text-slate-300 hover:text-white">Home</a>
            <a href="<?= site_url('services') ?>" class="block py-2 text-sm font-medium text-slate-300 hover:text-white">Services</a>
            <a href="<?= site_url('faq') ?>" class="block py-2 text-sm font-medium text-slate-300 hover:text-white">FAQ</a>
            <a href="<?= site_url('terms') ?>" class="block py-2 text-sm font-medium text-slate-300 hover:text-white">Terms</a>
            <div class="pt-2 border-t border-slate-800 flex flex-col gap-2">
                <?php if (!empty($user_session['is_logged_in'])): ?>
                    <a href="<?= site_url('dashboard') ?>" class="w-full text-center py-2.5 text-xs font-semibold rounded-xl bg-blue-600 text-white">Dashboard</a>
                <?php else: ?>
                    <a href="<?= site_url('login') ?>" class="w-full text-center py-2 text-xs font-medium text-slate-300 border border-slate-700 rounded-xl">Sign In</a>
                    <a href="<?= site_url('register') ?>" class="w-full text-center py-2.5 text-xs font-semibold rounded-xl bg-blue-600 text-white">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Global Footer -->
    <footer class="border-t border-slate-800/80 bg-[#050811] mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2.5 text-sm text-slate-400">
                <div class="w-6 h-6 rounded-md bg-blue-600 flex items-center justify-center text-white">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span>&copy; <?= date('Y') ?> <?= esc($site_name) ?>. All rights reserved.</span>
            </div>
            <div class="flex items-center gap-6 text-xs text-slate-400">
                <a href="<?= site_url('services') ?>" class="hover:text-slate-200 transition-colors">Services</a>
                <a href="<?= site_url('faq') ?>" class="hover:text-slate-200 transition-colors">FAQ</a>
                <a href="<?= site_url('terms') ?>" class="hover:text-slate-200 transition-colors">Terms of Service</a>
                <a href="<?= site_url('admin/login') ?>" class="text-slate-600 hover:text-slate-400 transition-colors">Portal</a>
            </div>
        </div>
    </footer>

</body>
</html>
