<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'ApexPulse Web Installer') ?></title>
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Alpine.js & Tailwind CSS 4 -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/resources/css/app.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #040711; color: #F1F5F9; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full bg-[#040711] text-slate-100 antialiased selection:bg-blue-600 selection:text-white flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 relative overflow-x-hidden">

    <!-- Ambient Electric Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[350px] bg-gradient-to-b from-blue-600/12 via-blue-500/5 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <div class="max-w-3xl w-full mx-auto space-y-6">
        
        <!-- Header Branding & Steps Indicator -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-[0_0_20px_rgba(37,99,235,0.45)]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-white">ApexPulse</span>
            </div>
            <p class="text-xs text-slate-400">Web-Based Platform Setup Wizard &bull; CodeIgniter 4 + MySQL 8.x</p>

            <!-- 8-Step Progress Indicator -->
            <?php 
                $currentStep = $step ?? 1; 
                $stepNames = [
                    1 => 'Welcome',
                    2 => 'Requirements',
                    3 => 'Database',
                    4 => 'Config',
                    5 => 'Admin',
                    6 => 'Database Install',
                    7 => 'Finalization',
                    8 => 'Complete',
                ];
            ?>
            <div class="pt-3">
                <div class="flex flex-wrap items-center justify-center gap-1 sm:gap-2 text-[10px] sm:text-[11px] font-mono">
                    <?php foreach ($stepNames as $num => $name): ?>
                        <div class="flex items-center gap-1">
                            <span class="px-2 py-0.5 rounded-md transition-colors <?= ($currentStep === $num) ? 'bg-blue-600 text-white font-bold shadow-[0_0_12px_rgba(37,99,235,0.5)]' : (($currentStep > $num) ? 'bg-blue-950/80 text-blue-300 border border-blue-600/30' : 'bg-slate-900/80 text-slate-600 border border-slate-800') ?>">
                                <?= $num ?>. <?= $name ?>
                            </span>
                            <?php if ($num < 8): ?>
                                <span class="text-slate-700 text-[10px] hidden sm:inline">&rarr;</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center justify-between">
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-xs flex items-center justify-between">
                <span><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs space-y-1">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <p>&bull; <?= esc($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Glassmorphism Main Installer Card -->
        <div class="rounded-2xl bg-[#080D1D]/90 border border-slate-800/80 p-6 sm:p-8 shadow-2xl backdrop-blur-md relative">
            <?= $this->renderSection('content') ?>
        </div>

        <!-- Quiet Footer -->
        <div class="text-center text-[11px] text-slate-500 font-mono">
            &copy; <?= date('Y') ?> ApexPulse SMM Engine. All sensitive credentials protected.
        </div>

    </div>

</body>
</html>
