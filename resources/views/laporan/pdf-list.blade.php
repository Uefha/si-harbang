<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 2px; color: #0d3b73; }
        .subjudul { font-size: 10px; color: #666; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #eaf2fb; color: #0d3b73; }
        .badge { padding: 1px 6px; border-radius: 8px; color: #fff; font-size: 9px; }
        .footer { margin-top: 16px; font-size: 9px; color: #888; text-align: right; }
        .kop { width: 100%; margin-bottom: 6px; border: none; }
        .kop td { vertical-align: middle; border: none; }
        .kop img { width: 56px; height: 56px; border-radius: 50%; object-fit: contain; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td style="width: 64px;">
                @if (file_exists(public_path('images/logo-sekolah.png')))
                    <img src="{{ public_path('images/logo-sekolah.png') }}" alt="Logo">
                @endif
            </td>
            <td>
                <h1>SI-HARBANG — Daftar Laporan Kerusakan</h1>
                <div class="subjudul">
                    SMA Taruna Nusantara &middot; Dicetak oleh {{ $dicetakOleh->name }} pada {{ $dicetakPada->translatedFormat('d F Y, H:i') }} WIB
                    &middot; Total {{ $laporan->count() }} laporan
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nomor Laporan</th>
                <th>Tanggal</th>
                <th>Pelapor</th>
                <th>Gedung / Lokasi</th>
                <th>Jenis Kerusakan</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $i => $l)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $l->nomor_laporan }}</td>
                    <td>{{ $l->created_at->format('d/m/Y') }}</td>
                    <td>{{ $l->nama_pelapor }}</td>
                    <td>{{ $l->gedung->nama_gedung }} — {{ $l->lokasi->nama_lokasi }}</td>
                    <td>{{ $l->jenisKerusakan->nama_jenis }}</td>
                    <td>{{ $l->prioritas?->nama ?? '-' }}</td>
                    <td>{{ $l->status->nama }}</td>
                    <td>{{ $l->petugasHarbang?->user?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;color:#888">Tidak ada laporan yang sesuai dengan filter.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">SI-HARBANG &middot; Sistem Informasi Pelaporan Kerusakan Sarana, Prasarana, dan Bangunan</div>
</body>
</html>
