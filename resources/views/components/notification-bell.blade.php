@php
    use App\Models\StatusLaporan;
    use App\Models\Laporan;

    $user = auth()->user();
    $tampilkan = $user->isSuperAdmin() || $user->isHarbang();
@endphp

@if ($tampilkan)
    @php
        $laporanBaru = Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::BARU))->count();
        $prioritasDarurat = Laporan::whereHas('prioritas', fn ($q) => $q->where('nama', 'Darurat'))
            ->whereHas('status', fn ($q) => $q->where('is_final', false))->count();
        $terlambat = Laporan::whereHas('status', fn ($q) => $q->where('is_final', false))->get()->filter->is_terlambat->count();
        $selesaiHariIni = Laporan::whereDate('tanggal_selesai', today())->count();
        $totalNotif = $laporanBaru + $prioritasDarurat + $terlambat;
    @endphp

    <div class="dropdown">
        <button class="btn btn-light position-relative" data-bs-toggle="dropdown" title="Notifikasi">
            <i class="bi bi-bell fs-5"></i>
            @if ($totalNotif > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                    {{ $totalNotif > 9 ? '9+' : $totalNotif }}
                </span>
            @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0" style="width: 300px;">
            <div class="px-3 py-2 border-bottom fw-medium small">Notifikasi</div>

            <a href="{{ route('laporan.index') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 {{ $laporanBaru == 0 ? 'text-muted' : '' }}">
                <span><i class="bi bi-inbox me-2"></i>Laporan baru belum diverifikasi</span>
                <span class="badge {{ $laporanBaru > 0 ? 'bg-secondary' : 'bg-light text-secondary' }} rounded-pill">{{ $laporanBaru }}</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 {{ $prioritasDarurat == 0 ? 'text-muted' : '' }}">
                <span><i class="bi bi-exclamation-triangle me-2"></i>Prioritas darurat aktif</span>
                <span class="badge {{ $prioritasDarurat > 0 ? 'bg-danger' : 'bg-light text-secondary' }} rounded-pill">{{ $prioritasDarurat }}</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 {{ $terlambat == 0 ? 'text-muted' : '' }}">
                <span><i class="bi bi-alarm me-2"></i>Terlambat ditangani</span>
                <span class="badge {{ $terlambat > 0 ? 'bg-danger' : 'bg-light text-secondary' }} rounded-pill">{{ $terlambat }}</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 text-muted">
                <span><i class="bi bi-check-circle me-2"></i>Selesai hari ini</span>
                <span class="badge bg-light text-secondary rounded-pill">{{ $selesaiHariIni }}</span>
            </a>

            @if ($totalNotif === 0)
                <div class="px-3 py-3 text-center text-secondary small">Tidak ada yang perlu perhatian segera 👍</div>
            @endif
        </div>
    </div>
@endif
