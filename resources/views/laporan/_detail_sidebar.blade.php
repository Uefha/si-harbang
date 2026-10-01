{{-- Kolom kanan: timeline & info penanganan. Variabel: $laporan --}}

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body text-center">
        @php
            // Staf discan untuk langsung membuka detail laporan; pelapor
            // (tidak punya akses ke URL staf) dapat nomor laporan saja,
            // cukup untuk dicari manual di halaman "Semua Laporan".
            $isiQr = auth()->user()->isPelapor()
                ? $laporan->nomor_laporan
                : route('laporan.show', $laporan);
        @endphp
        {!! QrCode::format('svg')->size(130)->margin(1)->generate($isiQr) !!}
        <div class="small text-secondary mt-1">{{ $laporan->nomor_laporan }}</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-medium">Timeline Penanganan</div>
    <div class="card-body">
        <ul class="list-unstyled mb-0">
            @forelse ($laporan->riwayatStatus as $riwayat)
                <li class="d-flex gap-2 pb-3">
                    <div class="rounded-circle bg-primary flex-shrink-0" style="width:10px;height:10px;margin-top:5px"></div>
                    <div>
                        <div class="fw-medium small">{{ $riwayat->status->nama }}</div>
                        <div class="text-secondary" style="font-size:.75rem">{{ $riwayat->created_at->translatedFormat('d M Y H:i') }} @if($riwayat->pengubah) &middot; {{ $riwayat->pengubah->name }} @endif</div>
                        @if ($riwayat->catatan)
                            <div class="small mt-1">{{ $riwayat->catatan }}</div>
                        @endif
                    </div>
                </li>
            @empty
                <li class="text-secondary small">Belum ada riwayat status.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-medium">Info Penanganan</div>
    <div class="card-body small">
        <div class="d-flex justify-content-between py-1">
            <span class="text-secondary">Petugas Harbang</span>
            <span class="fw-medium">{{ $laporan->petugasHarbang?->user?->name ?? '—' }}</span>
        </div>
        <div class="d-flex justify-content-between py-1">
            <span class="text-secondary">Estimasi Selesai</span>
            <span class="fw-medium">{{ $laporan->estimasi_selesai?->translatedFormat('d M Y') ?? '—' }}</span>
        </div>
        <div class="d-flex justify-content-between py-1">
            <span class="text-secondary">Tanggal Selesai</span>
            <span class="fw-medium">{{ $laporan->tanggal_selesai?->translatedFormat('d M Y H:i') ?? '—' }}</span>
        </div>
        <div class="d-flex justify-content-between py-1">
            <span class="text-secondary">Durasi Penanganan</span>
            <span class="fw-medium">{{ $laporan->durasi_penanganan ?? '—' }}</span>
        </div>
        @if ($laporan->catatan_harbang)
            <hr>
            <div class="text-secondary">Catatan Harbang</div>
            <div>{{ $laporan->catatan_harbang }}</div>
        @endif
    </div>
</div>
