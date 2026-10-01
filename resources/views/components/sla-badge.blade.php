{{-- Indikator SLA: badge merah "Terlambat Ditangani" bila laporan melewati tenggat --}}
@props(['laporan'])

@if ($laporan->is_terlambat)
    <span class="badge text-bg-danger badge-status"><i class="bi bi-exclamation-triangle me-1"></i>Terlambat Ditangani</span>
@elseif ($laporan->prioritas)
    <span class="badge text-bg-{{ $laporan->prioritas->warna_badge }} badge-status">{{ $laporan->prioritas->nama }}</span>
@endif
