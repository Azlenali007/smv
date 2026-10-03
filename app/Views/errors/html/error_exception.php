<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exception Occurred | ApexPulse</title>
    <link rel="stylesheet" href="<?= function_exists('base_url') ? base_url('assets/css/app.css') : '/assets/css/app.css' ?>">
    <style>
        body { font-family: sans-serif; background-color: #050811; color: #F8FAFC; padding: 2rem; margin: 0; }
        .box { max-width: 800px; margin: 0 auto; background: #080D1D; border: 1px solid #1E293B; border-radius: 12px; padding: 24px; }
        h1 { color: #F43F5E; font-size: 1.25rem; margin-top: 0; }
        pre { background: #03060E; padding: 12px; border-radius: 8px; overflow-x: auto; color: #94A3B8; font-size: 0.85rem; font-family: monospace; }
        a { color: #3B82F6; }
    </style>
</head>
<body>
    <div class="box">
        <h1>CodeIgniter Exception Caught</h1>
        <p><strong>Message:</strong> <?= esc($message ?? 'Unknown Exception') ?></p>
        <p><strong>File:</strong> <?= esc($file ?? '') ?> (Line: <?= esc($line ?? '') ?>)</p>
        <?php if (!empty($trace)): ?>
            <h3>Backtrace:</h3>
            <pre><?= esc(print_r($trace, true)) ?></pre>
        <?php endif; ?>
        <p><a href="/install">&rarr; Go to Web Installer</a> | <a href="/">&rarr; Homepage</a></p>
    </div>
</body>
</html>
