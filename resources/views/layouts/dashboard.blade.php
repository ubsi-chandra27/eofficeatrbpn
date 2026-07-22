<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Office ATR/BPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; }
        .nav-link { color: #bdc3c7; }
        .nav-link:hover { color: white; }
        .card { border-radius: 10px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 250px;">
            <h4 class="text-white text-center py-3">E-Office</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/surat/masuk">Surat Masuk</a></li>
                <li class="nav-item"><a class="nav-link" href="/surat/keluar">Surat Keluar</a></li>
                <li class="nav-item"><a class="nav-link" href="/surat/pengantar">Surat Pengantar</a></li>
                <li class="nav-item"><a class="nav-link" href="/surat/inisiatif">Surat Inisiatif</a></li>
                <li class="nav-item"><a class="nav-link" href="/disposisi">Disposisi</a></li>
            </ul>
        </div>
        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
