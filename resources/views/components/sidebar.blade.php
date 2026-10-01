@php
    use App\Models\Role;

    $user = auth()->user();

    // Setiap item: [label, icon, route name]. Jika route belum ada (fase
    // berikutnya belum dibangun), menu otomatis tampil non-aktif dengan
    // badge "segera" — tidak perlu diubah manual saat controller ditambah.
    $menuAdmin = [
        ['Dashboard', 'bi-speedometer2', 'dashboard'],
    ];
    $menuAdminUser = [
        ['Kelola User', 'bi-people', 'admin.users.index'],
        ['Kelola Pelapor', 'bi-person-badge', 'admin.pelapor.index'],
        ['Kelola Petugas Harbang', 'bi-person-gear', 'admin.petugas-harbang.index'],
    ];
    $menuMaster = [
        ['Gedung', 'bi-building', 'admin.gedung.index'],
        ['Lokasi', 'bi-geo-alt', 'admin.lokasi.index'],
        ['Jenis Kerusakan', 'bi-tools', 'admin.jenis-kerusakan.index'],
        ['Fasilitas', 'bi-box-seam', 'admin.fasilitas.index'],
        ['Prioritas', 'bi-flag', 'admin.prioritas.index'],
        ['Status Laporan', 'bi-list-check', 'admin.status.index'],
    ];
    $menuLaporanAdmin = [
        ['Semua Laporan', 'bi-file-earmark-text', 'laporan.index'],
        ['Riwayat Aktivitas', 'bi-clock-history', 'admin.aktivitas-log'],
        ['Pengaturan Sistem', 'bi-gear', 'admin.pengaturan.index'],
    ];
    $menuHarbang = [
        ['Dashboard', 'bi-speedometer2', 'dashboard'],
        ['Laporan Masuk', 'bi-inbox', 'laporan.index'],
    ];
    $menuPelapor = [
        ['Dashboard', 'bi-speedometer2', 'dashboard'],
        ['Buat Laporan', 'bi-plus-circle', 'lapor.create'],
        ['Laporan Saya', 'bi-file-earmark-text', 'lapor.index'],
    ];

    $renderItem = function (array $item) {
        [$label, $icon, $routeName] = $item;
        $exists = \Illuminate\Support\Facades\Route::has($routeName);
        $active = $exists && request()->routeIs($routeName) ? 'active' : '';

        if ($exists) {
            return '<a href="'.route($routeName).'" class="nav-link '.$active.'"><i class="bi '.$icon.' me-2"></i>'.$label.'</a>';
        }

        return '<span class="nav-link disabled d-flex justify-content-between align-items-center"><span><i class="bi '.$icon.' me-2"></i>'.$label.'</span><span class="badge bg-secondary bg-opacity-25 text-white-50" style="font-size:.6rem">segera</span></span>';
    };
@endphp

<nav class="nav flex-column py-2">
    @if ($user->isSuperAdmin())
        @foreach ($menuAdmin as $item) {!! $renderItem($item) !!} @endforeach

        <div class="nav-header">Manajemen Akun</div>
        @foreach ($menuAdminUser as $item) {!! $renderItem($item) !!} @endforeach

        <div class="nav-header">Data Master</div>
        @foreach ($menuMaster as $item) {!! $renderItem($item) !!} @endforeach

        <div class="nav-header">Laporan</div>
        @foreach ($menuLaporanAdmin as $item) {!! $renderItem($item) !!} @endforeach
    @elseif ($user->isHarbang())
        @foreach ($menuHarbang as $item) {!! $renderItem($item) !!} @endforeach
    @elseif ($user->isPelapor())
        @foreach ($menuPelapor as $item) {!! $renderItem($item) !!} @endforeach
    @endif
</nav>
