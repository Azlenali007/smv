<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-32 py-12 sm:py-20">

    <!-- 1. Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
        <div class="max-w-3xl mx-auto space-y-6">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                Direct Multi-Provider API Dispatch Engine
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight" style="text-wrap: balance;">
                Precision Social Media Marketing <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-blue-500">Infrastructure</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
                <?= esc($site_description) ?>. Automated order routing, atomic wallet balance safety, and transparent multi-currency exchange.
            </p>

            <!-- CTAs -->
            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <a href="<?= site_url('register') ?>" class="px-6 py-3.5 text-sm font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-[0_0_25px_rgba(37,99,235,0.4)] transition-all">
                    Create Free Account
                </a>
                <a href="<?= site_url('services') ?>" class="px-6 py-3.5 text-sm font-semibold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700/80 transition-all">
                    Explore Services & Pricing
                </a>
            </div>
        </div>

        <!-- Hero Graphic Showcase (CSS/SVG high-tech container) -->
        <div class="mt-16 max-w-5xl mx-auto rounded-2xl bg-gradient-to-b from-[#0B132B]/80 to-[#070B18]/90 border border-slate-800 p-2 sm:p-4 shadow-2xl relative overflow-hidden backdrop-blur-md">
            <div class="rounded-xl bg-[#060914] border border-slate-800/80 p-6 sm:p-8 text-left">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                        <span class="ml-2 text-xs font-mono text-slate-500">api.apexpulse.engine // order_pipeline</span>
                    </div>
                    <div class="text-xs text-blue-400 font-mono">STATUS: READY</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-[#0A1024]/60 border border-slate-800/60">
                        <p class="text-xs text-slate-500 font-medium">Core Architecture</p>
                        <p class="text-base font-semibold text-white mt-1">CodeIgniter 4 + MySQL 8</p>
                        <p class="text-xs text-slate-400 mt-2">Zero floating-point inaccuracies. DECIMAL precision on every ledger ledger row.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#0A1024]/60 border border-slate-800/60">
                        <p class="text-xs text-slate-500 font-medium">Currency Pipeline</p>
                        <p class="text-base font-semibold text-white mt-1">INR Base &rarr; Global Output</p>
                        <p class="text-xs text-slate-400 mt-2">Provider currency converted to INR, margin calculated, converted to user preference.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#0A1024]/60 border border-slate-800/60">
                        <p class="text-xs text-slate-500 font-medium">API Integration</p>
                        <p class="text-base font-semibold text-white mt-1">Multi-Provider cURL Sync</p>
                        <p class="text-xs text-slate-400 mt-2">Real SMM API v2 endpoints for automated balance, service import, and order tracking.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Features Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Platform Engineering Highlights</h2>
            <p class="text-sm text-slate-400 mt-2">Engineered strictly for production dependability and high throughput.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 hover:border-blue-500/30 transition-all space-y-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/15 border border-blue-500/25 flex items-center justify-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-white">Atomic Transaction Safety</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Every order debit, manual adjustment, deposit, and refund is executed inside an atomic MySQL transaction with strict lock-for-update mechanics.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 hover:border-blue-500/30 transition-all space-y-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/15 border border-blue-500/25 flex items-center justify-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-white">Real-Time Provider Sync</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Connect multiple upstream providers via standard SMM API v2. Pull live service catalogs, sync order updates, and map external service IDs safely.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 hover:border-blue-500/30 transition-all space-y-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/15 border border-blue-500/25 flex items-center justify-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-white">Dynamic Multi-Currency</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Fixed base INR valuation with live conversion to USD, EUR, GBP, and custom currencies. Users choose their display preference without pricing errors.
                </p>
            </div>

        </div>
    </section>

    <!-- 3. How It Works -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Order Lifecycle & Workflow</h2>
            <p class="text-sm text-slate-400 mt-2">Simple, automated, and trackable from placement to completion.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-3">
                <div class="text-blue-500 font-mono text-sm font-bold">01.</div>
                <h3 class="text-base font-semibold text-white">Create Account</h3>
                <p class="text-xs text-slate-400">Register in seconds with immediate wallet provisioning.</p>
            </div>

            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-3">
                <div class="text-blue-500 font-mono text-sm font-bold">02.</div>
                <h3 class="text-base font-semibold text-white">Deposit Funds</h3>
                <p class="text-xs text-slate-400">Add funds through verified gateways directly to your wallet.</p>
            </div>

            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-3">
                <div class="text-blue-500 font-mono text-sm font-bold">03.</div>
                <h3 class="text-base font-semibold text-white">Select Service</h3>
                <p class="text-xs text-slate-400">Choose from active services, enter your link and desired quantity.</p>
            </div>

            <div class="p-6 rounded-2xl bg-[#080D1D] border border-slate-800/80 space-y-3">
                <div class="text-blue-500 font-mono text-sm font-bold">04.</div>
                <h3 class="text-base font-semibold text-white">Automated Delivery</h3>
                <p class="text-xs text-slate-400">Your order is routed directly to the provider API and tracked live.</p>
            </div>
        </div>
    </section>

    <!-- 4. Services Overview (From Real MySQL Database) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Active Services Catalog</h2>
                <p class="text-sm text-slate-400 mt-1">Live rates configured in our database catalog.</p>
            </div>
            <a href="<?= site_url('services') ?>" class="text-xs font-semibold text-blue-400 hover:text-blue-300">
                View All Services &rarr;
            </a>
        </div>

        <?php if (!empty($services)): ?>
            <div class="rounded-2xl border border-slate-800/80 overflow-hidden bg-[#080D1D]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#050914] text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-4">Service</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4 font-mono text-right">Rate / 1k</th>
                                <th class="py-3 px-4 font-mono text-right">Min / Max</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            <?php foreach (array_slice($services, 0, 8) as $s): ?>
                                <tr class="hover:bg-slate-800/20 transition-colors">
                                    <td class="py-3.5 px-4 font-medium text-white max-w-xs truncate">
                                        <?= esc($s['name']) ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        <?= esc($s['category_name'] ?? 'General') ?>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-right text-blue-400 font-semibold tabular-nums">
                                        <?= $currency_service->format($currency_service->convertFromInr($s['rate_per_1k'], $current_currency), $current_currency) ?>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-right text-slate-400 tabular-nums">
                                        <?= number_format($s['min_quantity']) ?> / <?= number_format($s['max_quantity']) ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="<?= site_url('login') ?>" class="px-3 py-1 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-medium">
                                            Order
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <!-- Strict Empty State (No Fake Data) -->
            <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center bg-[#070B18]">
                <p class="text-sm font-medium text-slate-300">Catalog Initializing</p>
                <p class="text-xs text-slate-500 mt-1">Services configured by the administrator will automatically populate here.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- 5. FAQ Section -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Frequently Asked Questions</h2>
            <p class="text-sm text-slate-400 mt-2">Answers to common inquiries regarding our platform operations.</p>
        </div>

        <div class="space-y-4" x-data="{ openFaq: null }">
            
            <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
                <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full p-5 text-left flex items-center justify-between text-sm font-semibold text-white">
                    <span>How does the wallet balance calculation work?</span>
                    <span class="text-blue-400" x-text="openFaq === 1 ? '−' : '+'"></span>
                </button>
                <div x-show="openFaq === 1" x-cloak class="px-5 pb-5 text-xs text-slate-400 leading-relaxed">
                    All balances are stored internally in Indian Rupees (INR) using four decimal places (DECIMAL 16,4). When you view prices in USD, EUR, or GBP, rates are dynamically converted using administrator-configured exchange rates.
                </div>
            </div>

            <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
                <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full p-5 text-left flex items-center justify-between text-sm font-semibold text-white">
                    <span>Are API orders sent to providers in real time?</span>
                    <span class="text-blue-400" x-text="openFaq === 2 ? '−' : '+'"></span>
                </button>
                <div x-show="openFaq === 2" x-cloak class="px-5 pb-5 text-xs text-slate-400 leading-relaxed">
                    Yes. When an order is placed on a service mapped to an active provider, our backend immediately dispatches the cURL request to the upstream API, securely stores the remote order ID, and tracks provider updates.
                </div>
            </div>

            <div class="rounded-2xl bg-[#080D1D] border border-slate-800/80 overflow-hidden">
                <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full p-5 text-left flex items-center justify-between text-sm font-semibold text-white">
                    <span>What happens if an order fails or gets cancelled?</span>
                    <span class="text-blue-400" x-text="openFaq === 3 ? '−' : '+'"></span>
                </button>
                <div x-show="openFaq === 3" x-cloak class="px-5 pb-5 text-xs text-slate-400 leading-relaxed">
                    If an order is cancelled or refunded by administrators or providers, funds are credited back atomically to your wallet balance with an immutable transaction log.
                </div>
            </div>

        </div>
    </section>

    <!-- 6. Final Call To Action -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-gradient-to-r from-blue-900/30 via-[#0A1128] to-blue-900/20 border border-blue-500/20 p-8 sm:p-14 text-center space-y-6">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                Ready to Accelerate Your Social Marketing?
            </h2>
            <p class="text-sm text-slate-400 max-w-xl mx-auto">
                Join our robust platform with atomic order safety and multi-provider connectivity.
            </p>
            <div class="pt-2">
                <a href="<?= site_url('register') ?>" class="inline-block px-7 py-3.5 text-sm font-semibold rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-[0_0_30px_rgba(37,99,235,0.4)] transition-all">
                    Register Your Account Now
                </a>
            </div>
        </div>
    </section>

</div>
<?= $this->endSection() ?>
