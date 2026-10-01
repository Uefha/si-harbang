<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title', 'Masuk')</title>

    {{-- PWA: bisa dipasang sebagai aplikasi di Android & iOS --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d3b73">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="SI-HARBANG">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            min-height: 100vh; display: flex; align-items: center;
            background:
                linear-gradient(rgba(13,59,115,.82), rgba(13,59,115,.88)),
                url('{{ asset('images/bg-gedung.jpeg') }}') center / cover no-repeat fixed;
        }
        .auth-card { border: none; border-radius: 1rem; box-shadow: 0 1rem 3rem rgba(0,0,0,.25); }
        .auth-logo { width: 104px; height: 104px; border-radius: 50%; background: #fff; padding: 3px; object-fit: contain; box-shadow: 0 .25rem .75rem rgba(0,0,0,.25); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah" class="auth-logo mb-2">
                <div class="text-white fs-4 fw-bold">SI-HARBANG</div>
                <div class="text-white-50 small">Sistem Informasi Pelaporan Kerusakan<br>SMA Taruna Nusantara</div>
            </div>
            <div class="card auth-card">
                <div class="card-body p-4 p-sm-5">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }
</script>
</html>
