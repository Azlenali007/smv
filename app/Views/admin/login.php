<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Security Gateway | <?= esc($site_name) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/resources/css/app.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #03060F; color: #F1F5F9; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full bg-[#03060F] flex items-center justify-center p-4">

    <div class="max-w-md w-full rounded-2xl bg-[#070B17] border border-slate-800 p-8 shadow-2xl relative" x-data="{ showPass: false }">
        
        <div class="text-center mb-6 space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-600 mx-auto flex items-center justify-center text-white shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white tracking-tight">Admin Security Gateway</h1>
            <p class="text-xs text-slate-500">Restricted administrative access only.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-3 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="p-3 mb-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 text-xs">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/login') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Administrator Email</label>
                <input type="email" name="email" required class="w-full bg-[#040813] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-blue-500" placeholder="admin@apexpulse.io">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required class="w-full bg-[#040813] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-blue-500 pr-10">
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 text-xs">
                        <span x-text="showPass ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition-all shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                Authenticate Staff Access
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-600">
            &larr; <a href="<?= site_url('/') ?>" class="hover:text-slate-400">Return to Public Homepage</a>
        </div>

    </div>

</body>
</html>
