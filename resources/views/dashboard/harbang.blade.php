@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .si-stat-card { transition: transform .12s, box-shadow .12s; }
    .si-stat-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
</style>
@endpush

@section('content')
    <h4 class="fw-semibold mb-1">Dashboard Petugas Harbang</h4>
    <p class="text-secondary small mb-4">Selamat datang, {{ auth()->user()->name }}.</p>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Belum Diverifikasi', $belumDiverifikasi, 'bi-inbox', 'secondary', route('laporan.index', ['status_id' => $statusIdBaru])],
            ['Ditugaskan ke Saya', $ditugaskanKeSaya, 'bi-person-check', 'primary', $petugasId ? route('laporan.index', ['petugas_harbang_id' => $petugasId]) : route('laporan.index')],
            ['Terlambat Ditangani', $terlambat, 'bi-exclamation-triangle', 'danger', route('laporan.index', ['terlambat' => 1])],
            ['Selesai Hari Ini', $selesaiHariIni, 'bi-check-circle', 'success', route('laporan.index', ['status_id' => $statusIdSelesai])],
        ] as [$label, $value, $icon, $color, $href])
            <div class="col-6 col-md-3">
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-medium">Antrian Verifikasi</div>
        <div class="card-body p-0">
            @if ($antrianVerifikasi->isEmpty())
                <x-empty-state title="Tidak ada laporan menunggu verifikasi" icon="bi-check2-circle" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nomor Laporan</th>
                                <th>Gedung / Lokasi</th>
                                <th>Jenis Kerusakan</th>
                                <th>Tanggal Lapor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($antrianVerifikasi as $laporan)
                                <tr role="button" onclick="window.location='{{ route('laporan.show', $laporan) }}'" style="cursor:pointer">
                                    <td class="fw-medium">{{ $laporan->nomor_laporan }}</td>
                                    <td>{{ $laporan->gedung->nama_gedung }} — {{ $laporan->lokasi->nama_lokasi }}</td>
                                    <td>{{ $laporan->jenisKerusakan->nama_jenis }}</td>
                                    <td class="small text-secondary">{{ $laporan->created_at->translatedFormat('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
