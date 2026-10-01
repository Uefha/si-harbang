<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>

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
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SI-HARBANG">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --si-navy: #0d3b73; --si-blue: #1a5fa8; --si-blue-light: #eaf2fb; }
        body { background: #f4f7fb; }
        .si-sidebar { width: 240px; min-height: 100vh; background: var(--si-navy); position: fixed; top: 0; left: 0; z-index: 1030; transition: margin-left .2s; }
        .si-sidebar .brand { color: #fff; font-weight: 700; padding: .85rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,.1); display: flex; align-items: center; gap: .6rem; }
        .si-sidebar .brand img { width: 42px; height: 42px; border-radius: 50%; background: #fff; padding: 1px; object-fit: contain; flex-shrink: 0; }
        .si-sidebar .brand-text { font-size: .95rem; line-height: 1.15; }
        .si-sidebar .btn-close-sidebar { display: none; margin-left: auto; background: none; border: none; color: rgba(255,255,255,.7); font-size: 1.3rem; line-height: 1; padding: 0 .25rem; }
        .si-sidebar .btn-close-sidebar:hover { color: #fff; }
        .si-sidebar .nav-link { color: rgba(255,255,255,.75); padding: .6rem 1.25rem; font-size: .92rem; }
        .si-sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,.06); }
        .si-sidebar .nav-link.active { color: #fff; background: var(--si-blue); border-radius: 0; }
        .si-sidebar .nav-link.disabled { color: rgba(255,255,255,.35); }
        .si-sidebar .nav-header { color: rgba(255,255,255,.4); font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; padding: .9rem 1.25rem .25rem; }
        .si-content { margin-left: 240px; min-height: 100vh; }
        .si-topbar { background: #fff; border-bottom: 1px solid #e5e9f0; }
        @media (max-width: 991.98px) {
            .si-sidebar { margin-left: -240px; }
            .si-sidebar.show { margin-left: 0; }
            .si-sidebar.show .btn-close-sidebar { display: inline-block; }
            .si-content { margin-left: 0; }
            .si-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 1025; }
            .si-backdrop.show { display: block; }
        }
        .badge-status { font-weight: 500; }
    </style>
    @stack('styles')
</head>
<body>

<aside class="si-sidebar" id="siSidebar">
    <div class="brand">
        <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah">
        <span class="brand-text">SI-HARBANG<br><small class="fw-normal" style="font-size:.65rem;opacity:.7">SMA Taruna Nusantara</small></span>
        <button type="button" class="btn-close-sidebar d-lg-none" id="btnCloseSidebar" aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <x-sidebar />
</aside>

<div class="si-backdrop" id="siBackdrop"></div>

<div class="si-content">
    <nav class="si-topbar navbar navbar-expand px-3 py-2 sticky-top">
        <button class="btn btn-light d-lg-none me-2" type="button" id="btnOpenSidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="ms-auto d-flex align-items-center gap-3">
            <x-notification-bell />
            <span class="small text-secondary d-none d-sm-inline">{{ auth()->user()->role?->display_name }}</span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-secondary">{{ auth()->user()->email }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid p-3 p-lg-4">
        @isset($breadcrumb)
            <x-breadcrumb :items="$breadcrumb" />
        @endisset

        @yield('content')
    </main>
</div>

{{-- Modal konfirmasi hapus - dipakai ulang otomatis oleh semua tombol/form
     bertanda class "form-hapus" di seluruh aplikasi (lihat skrip di bawah) --}}
<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center pt-4 pb-2">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 mb-3" style="width:64px;height:64px;">
                    <i class="bi bi-trash3 text-danger" style="font-size:1.6rem;"></i>
                </div>
                <h5 class="fw-semibold mb-2">Hapus Data Ini?</h5>
                <p class="text-secondary small mb-0" id="teksKonfirmasiHapus">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger px-4" id="btnKonfirmasiHapus">
                    <i class="bi bi-trash3 me-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
    @if (session('success'))
        <x-toast type="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-toast type="danger" :message="session('error')" />
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Daftarkan service worker supaya browser menawarkan "Instal Aplikasi" / "Tambahkan ke Layar Utama"
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }
</script>
<script>
    // Buka/tutup sidebar di layar sempit (tombol hamburger, tombol X, dan klik di luar sidebar)
    const sidebar = document.getElementById('siSidebar');
    const backdrop = document.getElementById('siBackdrop');

    function openSidebar() {
        sidebar.classList.add('show');
        backdrop.classList.add('show');
    }
    function closeSidebar() {
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
    }

    document.getElementById('btnOpenSidebar')?.addEventListener('click', openSidebar);
    document.getElementById('btnCloseSidebar')?.addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);

    // Konfirmasi sebelum hapus data - berlaku otomatis di semua halaman
    // untuk setiap <form class="form-hapus"> (dipakai di seluruh CRUD data
    // master). Pakai modal Bootstrap sendiri, bukan confirm() bawaan browser
    // yang tampilannya polos dan tidak bisa diberi gaya.
    const modalHapusEl = document.getElementById('modalKonfirmasiHapus');
    const modalHapus = new bootstrap.Modal(modalHapusEl);
    const teksKonfirmasiHapus = document.getElementById('teksKonfirmasiHapus');
    let formYangAkanDihapus = null;

    document.addEventListener('submit', function (e) {
        if (e.target.matches('.form-hapus') && !e.target.dataset.terkonfirmasi) {
            e.preventDefault();
            formYangAkanDihapus = e.target;
            teksKonfirmasiHapus.textContent = e.target.dataset.confirmMessage
                || 'Tindakan ini tidak dapat dibatalkan.';
            modalHapus.show();
        }
    });

    document.getElementById('btnKonfirmasiHapus').addEventListener('click', function () {
        if (!formYangAkanDihapus) return;
        formYangAkanDihapus.dataset.terkonfirmasi = '1';
        modalHapus.hide();
        formYangAkanDihapus.requestSubmit();
        formYangAkanDihapus = null;
    });

    // Reset penanda kalau modal ditutup tanpa konfirmasi (klik Batal/luar modal)
    modalHapusEl.addEventListener('hidden.bs.modal', function () {
        if (formYangAkanDihapus) {
            delete formYangAkanDihapus.dataset.terkonfirmasi;
            formYangAkanDihapus = null;
        }
    });

    // Auto-tutup toast notification bawaan setelah beberapa detik
    document.querySelectorAll('.toast.show').forEach(function (el) {
        new bootstrap.Toast(el).show();
    });

    // Kalau submit form modal (Tambah/Ubah) gagal validasi, Laravel redirect
    // balik dengan pesan error - tapi modal Bootstrap-nya otomatis tertutup
    // lagi setelah reload, jadi user tidak melihat pesan errornya sama
    // sekali. Perbaikan generik: kalau ada field bertanda "is-invalid" di
    // halaman (dirender lewat @@error() Blade), buka modal yang memuatnya.
    @if ($errors->any())
        (function () {
            const fieldInvalid = document.querySelector('.is-invalid');
            const modal = fieldInvalid?.closest('.modal');
            if (modal) new bootstrap.Modal(modal).show();
        })();
    @endif
</script>
@stack('scripts')
</body>
</html>
