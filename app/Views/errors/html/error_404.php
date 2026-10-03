<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Not Found | ApexPulse</title>
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
        <span class="text-4xl font-extrabold text-blue-500 font-mono">404</span>
        <div class="space-y-2">
            <h1 class="text-xl font-bold text-white tracking-tight">Endpoint Not Located</h1>
            <p class="text-xs text-slate-400 leading-relaxed">
                <?= esc($message ?? 'The requested resource or page does not exist on this server.') ?>
            </p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <a href="/" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_15px_rgba(37,99,235,0.3)]">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
