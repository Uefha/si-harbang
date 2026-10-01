@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .si-stat-card { transition: transform .12s, box-shadow .12s; }
    .si-stat-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
</style>
@endpush

@section('content')
    <h4 class="fw-semibold mb-1">Dashboard Pelapor</h4>
    <p class="text-secondary small mb-4">Selamat datang, {{ auth()->user()->name }}.</p>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Total Laporan Saya', $totalLaporanSaya, 'bi-file-earmark-text', 'primary'],
            ['Sedang Diproses', $sedangDiproses, 'bi-hourglass-split', 'warning'],
            ['Selesai', $selesai, 'bi-check-circle', 'success'],
        ] as [$label, $value, $icon, $color])
            <div class="col-6 col-md-4">
                <a href="{{ route('lapor.index') }}" class="text-decoration-none">
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-medium d-flex justify-content-between align-items-center">
            Laporan Terbaru Saya
        </div>
        <div class="card-body p-0">
            @if ($laporanTerbaru->isEmpty())
                <x-empty-state title="Anda belum pernah membuat laporan" description="Klik &quot;Buat Laporan&quot; di menu samping untuk melapor kerusakan." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nomor Laporan</th>
                                <th>Gedung</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporanTerbaru as $laporan)
                                <tr role="button" onclick="window.location='{{ route('lapor.show', $laporan) }}'" style="cursor:pointer">
                                    <td class="fw-medium">{{ $laporan->nomor_laporan }}</td>
                                    <td>{{ $laporan->gedung->nama_gedung }}</td>
                                    <td><x-status-badge :status="$laporan->status" /></td>
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
