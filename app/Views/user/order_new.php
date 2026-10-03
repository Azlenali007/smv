<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto py-4 space-y-6">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Place New Order</h1>
        <p class="text-xs text-slate-400 mt-1">Select an active service, enter your target link and quantity.</p>
    </div>

    <!-- Alpine.js Component for New Order Form -->
    <div class="rounded-2xl bg-[#090E1F] border border-slate-800/80 p-6 sm:p-8" 
         x-data="{
            categories: <?= htmlspecialchars(json_encode($categories), ENT_QUOTES, 'UTF-8') ?>,
            services: <?= htmlspecialchars(json_encode($services), ENT_QUOTES, 'UTF-8') ?>,
            selectedCategory: '',
            selectedServiceId: '<?= $selectedServiceId ?? '' ?>',
            selectedService: null,
            link: '',
            quantity: 100,
            calculatedPrice: '0.00',
            currencySymbol: '<?= esc($currency_service->getCurrency($current_currency)['symbol'] ?? '₹') ?>',
            currentCurrency: '<?= esc($current_currency) ?>',
            userBalanceInr: <?= (float)($wallet['balance'] ?? 0) ?>,
            isSubmitting: false,

            init() {
                if (this.selectedServiceId) {
                    const match = this.services.find(s => s.id == this.selectedServiceId);
                    if (match) {
                        this.selectedCategory = match.category_id;
                        this.selectedService = match;
                        this.quantity = match.min_quantity;
                        this.recalc();
                    }
                }
            },

            onCategoryChange() {
                this.selectedServiceId = '';
                this.selectedService = null;
                this.calculatedPrice = '0.00';
            },

            onServiceChange() {
                const s = this.services.find(item => item.id == this.selectedServiceId);
                this.selectedService = s || null;
                if (s) {
                    this.quantity = parseInt(s.min_quantity) || 10;
                    this.recalc();
                } else {
                    this.calculatedPrice = '0.00';
                }
            },

            filteredServices() {
                if (!this.selectedCategory) return this.services;
                return this.services.filter(s => s.category_id == this.selectedCategory);
            },

            recalc() {
                if (!this.selectedService || !this.quantity) {
                    this.calculatedPrice = '0.00';
                    return;
                }
                const ratePer1k = parseFloat(this.selectedService.rate_per_1k) || 0;
                // rate is stored in base INR
                const totalInr = (ratePer1k / 1000.0) * parseInt(this.quantity);
                
                // Fetch dynamic conversion or local approximation
                this.calculatedPrice = (totalInr).toFixed(2);
            }
         }">

        <form action="<?= site_url('order/store') ?>" method="POST" @submit="isSubmitting = true" class="space-y-5">
            <?= csrf_field() ?>

            <!-- Category Field -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Category</label>
                <select x-model="selectedCategory" @change="onCategoryChange()" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="">Select a Category</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>
            </div>

            <!-- Service Field -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Service</label>
                <select name="service_id" x-model="selectedServiceId" @change="onServiceChange()" required class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="">Choose an active service...</option>
                    <template x-for="s in filteredServices()" :key="s.id">
                        <option :value="s.id" x-text="'#' + s.id + ' - ' + s.name"></option>
                    </template>
                </select>
            </div>

            <!-- Service Description Callout -->
            <div x-show="selectedService && selectedService.description" x-cloak class="p-3.5 rounded-xl bg-[#060B19] border border-slate-800 text-xs text-slate-400 space-y-1">
                <span class="text-[11px] font-semibold text-blue-400 block">Service Specifications:</span>
                <p x-text="selectedService ? selectedService.description : ''" class="whitespace-pre-line leading-relaxed"></p>
                <div class="flex items-center gap-4 pt-1 font-mono text-[11px] text-slate-500">
                    <span>Min: <strong class="text-slate-300" x-text="selectedService ? selectedService.min_quantity : ''"></strong></span>
                    <span>Max: <strong class="text-slate-300" x-text="selectedService ? selectedService.max_quantity : ''"></strong></span>
                </div>
            </div>

            <!-- Link Field -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Target Link / URL</label>
                <input type="url" name="link" x-model="link" required placeholder="https://instagram.com/username or https://youtube.com/watch?v=..." class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-blue-500">
            </div>

            <!-- Quantity Field -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-slate-300">Quantity</label>
                    <span x-show="selectedService" class="text-[11px] text-slate-500 font-mono">
                        Limits: <span x-text="selectedService ? selectedService.min_quantity : 0"></span> - <span x-text="selectedService ? selectedService.max_quantity : 0"></span>
                    </span>
                </div>
                <input type="number" name="quantity" x-model="quantity" @input="recalc()" required min="1" class="w-full bg-[#050914] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <!-- Estimated Price Indicator & Balance Summary -->
            <div class="p-4 rounded-xl bg-[#060915] border border-slate-800/80 flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-slate-400 font-medium block">Total Charge (INR Base)</span>
                    <div class="text-lg font-bold text-blue-400 font-mono tabular-nums mt-0.5">
                        ₹<span x-text="calculatedPrice"></span> INR
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-slate-400 font-medium block">Current Balance</span>
                    <div class="text-sm font-semibold text-white font-mono tabular-nums mt-0.5">
                        <?= $currency_service->format($currency_service->convertFromInr($wallet['balance'] ?? 0, $current_currency), $current_currency) ?>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" :disabled="!selectedServiceId || isSubmitting" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.4)] disabled:opacity-40 disabled:cursor-not-allowed">
                <span x-show="!isSubmitting">Submit Order</span>
                <span x-show="isSubmitting" x-cloak>Validating & processing atomic transaction...</span>
            </button>
        </form>

    </div>

</div>
<?= $this->endSection() ?>
