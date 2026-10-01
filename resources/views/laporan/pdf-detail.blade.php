<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 15px; margin: 0 0 2px; color: #0d3b73; }
        .subjudul { font-size: 10px; color: #666; margin-bottom: 12px; }
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.info td { padding: 3px 4px; vertical-align: top; }
        table.info td.label { width: 32%; color: #666; }
        .kotak { border: 1px solid #ccc; border-radius: 4px; padding: 8px 10px; margin-bottom: 10px; }
        .kotak-judul { font-weight: bold; color: #0d3b73; font-size: 11px; margin-bottom: 6px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        .badge { padding: 2px 8px; border-radius: 8px; color: #fff; font-size: 10px; }
        .foto-grid img { width: 100px; height: 100px; object-fit: cover; margin: 3px; border: 1px solid #ccc; border-radius: 3px; }
        .riwayat { border-left: 2px solid #1a5fa8; padding-left: 8px; margin-bottom: 6px; }
        .footer { margin-top: 20px; font-size: 9px; color: #888; text-align: right; }
        .ttd { margin-top: 40px; width: 100%; }
        .ttd td { text-align: center; padding-top: 40px; font-size: 10px; }
        .kop { width: 100%; margin-bottom: 8px; }
        .kop td { vertical-align: middle; }
        .kop img { width: 58px; height: 58px; border-radius: 50%; object-fit: contain; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td style="width: 66px;">
                @if (file_exists(public_path('images/logo-sekolah.png')))
                    <img src="{{ public_path('images/logo-sekolah.png') }}" alt="Logo">
                @endif
            </td>
            <td>
                <h1>SI-HARBANG — Detail Laporan Kerusakan</h1>
                <div class="subjudul">SMA Taruna Nusantara &middot; Dicetak {{ $dicetakPada->translatedFormat('d F Y, H:i') }} WIB oleh {{ $dicetakOleh->name }}</div>
            </td>
        </tr>
    </table>

    <div class="kotak">
        <div class="kotak-judul">{{ $laporan->nomor_laporan }} &middot; {{ $laporan->status->nama }}</div>
        <table class="info">
            <tr>
                <td class="label">Pelapor</td>
                <td>{{ $laporan->nama_pelapor }} ({{ $laporan->jabatan }}) &middot; {{ $laporan->no_hp }}</td>
                <td class="label">Gedung / Lokasi</td>
                <td>{{ $laporan->gedung->nama_gedung }} — {{ $laporan->lokasi->nama_lokasi }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kerusakan</td>
                <td>{{ $laporan->jenisKerusakan->nama_jenis }}</td>
                <td class="label">Fasilitas/Barang</td>
                <td>{{ $laporan->fasilitas?->nama_fasilitas ?? $laporan->nama_fasilitas_lainnya ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Prioritas</td>
                <td>{{ $laporan->prioritas?->nama ?? 'Belum diverifikasi' }}</td>
                <td class="label">Petugas Harbang</td>
                <td>{{ $laporan->petugasHarbang?->user?->name ?? 'Belum ditugaskan' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kejadian</td>
                <td>{{ $laporan->tanggal_kejadian->translatedFormat('d F Y') }}, {{ \Illuminate\Support\Carbon::parse($laporan->waktu_kejadian)->format('H:i') }}</td>
                <td class="label">Estimasi / Selesai</td>
                <td>{{ $laporan->estimasi_selesai?->translatedFormat('d M Y') ?? '-' }} / {{ $laporan->tanggal_selesai?->translatedFormat('d M Y H:i') ?? '-' }}</td>
            </tr>
        </table>
        <div class="label" style="color:#666">Deskripsi Kerusakan</div>
        <div>{{ $laporan->deskripsi_kerusakan }}</div>
        @if ($laporan->catatan_harbang)
            <div class="label" style="color:#666;margin-top:6px">Catatan Harbang</div>
            <div>{{ $laporan->catatan_harbang }}</div>
        @endif
    </div>

    @foreach ([['Foto Kerusakan', $laporan->fotoKerusakan], ['Foto Proses Perbaikan', $laporan->fotoProses], ['Foto Hasil Perbaikan', $laporan->fotoSelesai]] as [$judul, $fotos])
        @if ($fotos->isNotEmpty())
            <div class="kotak">
                <div class="kotak-judul">{{ $judul }}</div>
                <div class="foto-grid">
                    @foreach ($fotos as $foto)
                        @if ($foto->is_video)
                            <div style="display:inline-block;width:100px;height:100px;margin:3px;border:1px solid #ccc;border-radius:3px;text-align:center;vertical-align:top;padding-top:38px;font-size:9px;color:#666;">
                                &#9654; Video<br>(lihat di sistem)
                            </div>
                        @else
                            @php $path = public_path($foto->path); @endphp
                            @if (file_exists($path))
                                <img src="{{ $path }}">
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    <div class="kotak">
        <div class="kotak-judul">Riwayat Status</div>
        @foreach ($laporan->riwayatStatus->sortBy('created_at') as $riwayat)
            <div class="riwayat">
                <strong>{{ $riwayat->status->nama }}</strong> — {{ $riwayat->created_at->translatedFormat('d M Y H:i') }}
                @if ($riwayat->pengubah) &middot; {{ $riwayat->pengubah->name }} @endif
                @if ($riwayat->catatan)<br><span style="color:#555">{{ $riwayat->catatan }}</span>@endif
            </div>
        @endforeach
    </div>

    <table class="ttd">
        <tr>
            <td>Pelapor,<br><br><br>( {{ $laporan->nama_pelapor }} )</td>
            <td>Petugas Harbang,<br><br><br>( {{ $laporan->petugasHarbang?->user?->name ?? '........................' }} )</td>
        </tr>
    </table>

    <div class="footer">SI-HARBANG &middot; Sistem Informasi Pelaporan Kerusakan Sarana, Prasarana, dan Bangunan</div>
</body>
</html>
