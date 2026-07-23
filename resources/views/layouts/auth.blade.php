<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ \App\Models\Setting::getValue('app_name','E-Office') }}</title>
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <main class="auth-page">
        <div class="auth-shell">
            <aside class="auth-brand">
                <div>
                    <h1 class="brand-name">{{ \App\Models\Setting::getValue('app_name','E-Office') }}</h1>
                    <p class="brand-subtitle">{{ \App\Models\Setting::getValue('app_subtitle','Administrasi Digital') }}</p>
                </div>
                <div class="brand-content">
                    <h2>Persuratan dalam satu ruang kerja digital.</h2>
                    <p>Kelola surat, disposisi, dan arsip secara terstruktur sesuai hak akses setiap pengguna.</p>
                    <div class="brand-features">
                        <div class="brand-feature"><i class="bi bi-folder-check"></i>Arsip tersusun dan mudah ditemukan</div>
                        <div class="brand-feature"><i class="bi bi-shield-check"></i>Akses terpisah berdasarkan peran</div>
                        <div class="brand-feature"><i class="bi bi-clock-history"></i>Aktivitas tercatat sebagai histori</div>
                    </div>
                </div>
                <div class="brand-footer">© {{ date('Y') }} {{ \App\Models\Setting::getValue('app_name','E-Office') }}</div>
            </aside>
            <section class="auth-content">
                <div class="auth-content-inner">
                    @yield('content')
                </div>
            </section>
        </div>
    </main>
    @stack('scripts')
</body>
</html>
