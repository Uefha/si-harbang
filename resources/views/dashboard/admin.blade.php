@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .si-stat-card { transition: transform .12s, box-shadow .12s; }
    .si-stat-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
</style>
@endpush

@section('content')
    <h4 class="fw-semibold mb-1">Dashboard Super Admin</h4>
    <p class="text-secondary small mb-4">Ringkasan seluruh laporan kerusakan di lingkungan SMA Taruna Nusantara.</p>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Total Laporan', $totalLaporan, 'bi-file-earmark-text', 'primary', route('laporan.index')],
            ['Laporan Baru', $laporanBaru, 'bi-inbox', 'secondary', route('laporan.index', ['status_id' => $statusId[\App\Models\StatusLaporan::BARU] ?? null])],
            ['Diverifikasi', $sedangDiverifikasi, 'bi-patch-check', 'info', route('laporan.index', ['status_id' => $statusId[\App\Models\StatusLaporan::DIVERIFIKASI] ?? null])],
            ['Sedang Dikerjakan', $sedangDikerjakan, 'bi-tools', 'primary', route('laporan.index', ['status_id' => $statusId[\App\Models\StatusLaporan::SEDANG_DIKERJAKAN] ?? null])],
            ['Menunggu Sparepart', $menungguSparepart, 'bi-box-seam', 'dark', route('laporan.index', ['status_id' => $statusId[\App\Models\StatusLaporan::MENUNGGU_MATERIAL] ?? null])],
            ['Selesai', $selesai, 'bi-check-circle', 'success', route('laporan.index', ['status_id' => $statusId[\App\Models\StatusLaporan::SELESAI] ?? null])],
            ['Ditolak', $ditolak, 'bi-x-circle', 'danger', route('laporan.index', ['status_id' => $statusId[\App\Models\StatusLaporan::DITOLAK] ?? null])],
            ['Prioritas Tinggi/Darurat', $prioritasTinggi, 'bi-exclamation-triangle', 'warning', route('laporan.index', ['prioritas_id' => $prioritasUrgentIds])],
            ['Terlambat Ditangani', $terlambat, 'bi-alarm', 'danger', route('laporan.index', ['terlambat' => 1])],
        ] as [$label, $value, $icon, $color, $href])
            <div class="col-6 col-md-4">
                <a href="{{ $href }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 si-stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-bg-{{ $color }}" style="width:44px;height:44px;">
                                <i class="bi {{ $icon }} fs-5"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-semibold lh-1 text-dark">{{ $value }}</div>
                                <div class="small text-secondary">{{ $label }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-medium">Laporan per Bulan (12 Bulan Terakhir)</div>
                <div class="card-body">
                    <div style="max-width: 520px; margin: 0 auto;">
                        <canvas id="chartBulan" height="70"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-medium">Laporan per Prioritas</div>
                <div class="card-body">
                    <div style="max-width: 260px; margin: 0 auto;">
                        <canvas id="chartPrioritas" height="130"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-medium">Laporan per Gedung</div>
                <div class="card-body">
                    @if ($chartGedung['labels']->isEmpty())
                        <x-empty-state title="Belum ada data laporan" icon="bi-building" />
                    @else
                        <div style="max-width: 420px; margin: 0 auto;">
                            <canvas id="chartGedung" height="120"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-medium">Laporan per Jenis Kerusakan</div>
                <div class="card-body">
                    @if ($chartJenis['labels']->isEmpty())
                        <x-empty-state title="Belum ada data laporan" icon="bi-tools" />
                    @else
                        <div style="max-width: 260px; margin: 0 auto;">
                            <canvas id="chartJenis" height="130"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-medium">Laporan Terbaru</div>
        <div class="card-body p-0">
            @if ($laporanTerbaru->isEmpty())
                <x-empty-state title="Belum ada laporan masuk" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nomor Laporan</th>
                                <th>Gedung</th>
                                <th>Status</th>
                                <th>Prioritas</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporanTerbaru as $laporan)
                                <tr role="button" onclick="window.location='{{ route('laporan.show', $laporan) }}'" style="cursor:pointer">
                                    <td class="fw-medium">{{ $laporan->nomor_laporan }}</td>
                                    <td>{{ $laporan->gedung->nama_gedung }}</td>
                                    <td><x-status-badge :status="$laporan->status" /></td>
                                    <td><x-sla-badge :laporan="$laporan" /></td>
                                    <td class="small text-secondary">{{ $laporan->created_at->translatedFormat('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
// Palet warna Bootstrap -> hex, dipakai supaya warna grafik selaras dengan
// badge warna_badge yang sama di seluruh aplikasi (lihat komponen status-badge/sla-badge).
const warnaBootstrap = {
    primary: '#0d6efd', secondary: '#6c757d', success: '#198754',
    danger: '#dc3545', warning: '#ffc107', info: '#0dcaf0', dark: '#212529',
};
const paletDefault = ['#1a5fa8', '#0dcaf0', '#198754', '#ffc107', '#dc3545', '#6c757d', '#343a40', '#0d6efd'];

new Chart(document.getElementById('chartBulan'), {
    type: 'line',
    data: {
        labels: @json($chartBulan['labels']),
        datasets: [{
            label: 'Jumlah Laporan',
            data: @json($chartBulan['data']),
            borderColor: '#1a5fa8',
            backgroundColor: 'rgba(26,95,168,.12)',
            tension: 0.3,
            fill: true,
            pointRadius: 3,
        }],
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
    },
});

@if ($chartPrioritas['labels']->isNotEmpty())
new Chart(document.getElementById('chartPrioritas'), {
    type: 'doughnut',
    data: {
        labels: @json($chartPrioritas['labels']),
        datasets: [{
            data: @json($chartPrioritas['data']),
            backgroundColor: @json($chartPrioritas['warna']).map(w => warnaBootstrap[w] ?? '#6c757d'),
        }],
    },
    options: { plugins: { legend: { position: 'bottom' } } },
});
@endif

@if ($chartGedung['labels']->isNotEmpty())
new Chart(document.getElementById('chartGedung'), {
    type: 'bar',
    data: {
        labels: @json($chartGedung['labels']),
        datasets: [{ label: 'Jumlah Laporan', data: @json($chartGedung['data']), backgroundColor: '#1a5fa8', borderRadius: 4 }],
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
    },
});
@endif

@if ($chartJenis['labels']->isNotEmpty())
new Chart(document.getElementById('chartJenis'), {
    type: 'doughnut',
    data: {
        labels: @json($chartJenis['labels']),
        datasets: [{ data: @json($chartJenis['data']), backgroundColor: paletDefault }],
    },
    options: { plugins: { legend: { position: 'bottom' } } },
});
@endif
</script>
@endpush
