{{-- Badge status laporan, warna diambil dari data master status_laporan --}}
@props(['status'])

<span class="badge badge-status text-bg-{{ $status->warna_badge }}">{{ $status->nama }}</span>
