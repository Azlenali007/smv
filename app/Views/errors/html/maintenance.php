<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Maintenance | <?= esc($site_name ?? 'ApexPulse') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/resources/css/app.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #040711; color: #F8FAFC; }
    </style>
</head>
<body class="h-full bg-[#040711] flex items-center justify-center p-4">

    <div class="max-w-md w-full text-center space-y-6">
        <div class="w-16 h-16 rounded-2xl bg-blue-600/20 border border-blue-500/30 mx-auto flex items-center justify-center text-blue-400 shadow-[0_0_30px_rgba(37,99,235,0.25)]">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-white tracking-tight">Platform Maintenance in Progress</h1>
            <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                <?= esc($message ?? 'We are currently undergoing scheduled upgrades to optimize performance and security. We will be back online shortly.') ?>
            </p>
        </div>

        <div class="p-4 rounded-xl bg-[#070B18] border border-slate-800 text-[11px] text-slate-500 font-mono">
            HTTP 503 SERVICE UNAVAILABLE // SYSTEM UPGRADE
        </div>
    </div>

</body>
</html>
