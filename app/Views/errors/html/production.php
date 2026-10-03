<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error | ApexPulse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= function_exists('base_url') ? base_url('assets/css/app.css') : '/assets/css/app.css' ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #050811; color: #F8FAFC; margin: 0; padding: 0; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen bg-[#050811] text-slate-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center space-y-5 p-8 rounded-2xl bg-[#080D1D] border border-slate-800 shadow-2xl">
        <div class="w-14 h-14 rounded-2xl bg-rose-600/20 border border-rose-500/30 text-rose-400 mx-auto flex items-center justify-center shadow-[0_0_25px_rgba(244,63,94,0.3)]">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="space-y-2">
            <h1 class="text-xl font-bold text-white tracking-tight">System Encountered An Issue</h1>
            <p class="text-xs text-slate-400 leading-relaxed">
                An internal exception occurred while processing your request. If this is a fresh setup, please ensure the database is configured via <a href="/install" class="text-blue-400 underline">/install</a>.
            </p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <a href="/" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_15px_rgba(37,99,235,0.3)]">Return Home</a>
            <a href="/install" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors">Platform Installer</a>
        </div>
    </div>
</body>
</html>
