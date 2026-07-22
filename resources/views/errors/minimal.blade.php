<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('code') | E-Office</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#f1f5f9;color:#1e293b;font-family:system-ui,-apple-system,"Segoe UI",sans-serif}.error-card{width:min(540px,100%);padding:42px;text-align:center;background:#fff;border:1px solid #e2e8f0;border-radius:20px;box-shadow:0 18px 45px rgba(15,23,42,.08)}.icon{width:70px;height:70px;margin:auto;display:grid;place-items:center;border-radius:18px;background:#eaf4ff;color:#0f5d9b;font-size:25px;font-weight:800}.code{margin:18px 0 3px;color:#0f5d9b;font-size:13px;font-weight:800;letter-spacing:1.5px}.error-card h1{margin:0;font-size:27px}.error-card p{color:#64748b;line-height:1.65}.actions{display:flex;justify-content:center;gap:10px;margin-top:24px}.actions a,.actions button{padding:11px 18px;border:1px solid #0f5d9b;border-radius:10px;background:#0f5d9b;color:#fff;text-decoration:none;font:inherit;font-weight:650;cursor:pointer}.actions .secondary{background:#fff;color:#0f5d9b}@media(max-width:520px){.error-card{padding:30px 21px}.actions{flex-direction:column}}
    </style>
</head>
<body><main class="error-card"><div class="icon">@yield('code')</div><div class="code">E-OFFICE</div><h1>@yield('title')</h1><p>@yield('message')</p><div class="actions"><button class="secondary" onclick="history.back()">Kembali</button><a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}">{{ auth()->check() ? 'Ke Dashboard' : 'Ke Login' }}</a></div></main></body>
</html>
