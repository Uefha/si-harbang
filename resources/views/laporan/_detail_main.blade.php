{{-- Kolom kiri: info utama, foto, komentar. Variabel: $laporan --}}

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <div class="text-secondary small">Nomor Laporan</div>
                <div class="fs-5 fw-semibold">{{ $laporan->nomor_laporan }}</div>
            </div>
            <div class="d-flex gap-2">
                <x-status-badge :status="$laporan->status" />
                <x-sla-badge :laporan="$laporan" />
            </div>
        </div>

        <div class="row g-3 small">
            <div class="col-sm-6">
                <div class="text-secondary">Pelapor</div>
                <div class="fw-medium">{{ $laporan->nama_pelapor }}</div>
                <div class="text-secondary">{{ $laporan->jabatan }} @if($laporan->nip_nik) &middot; {{ $laporan->nip_nik }} @endif</div>
                <div class="text-secondary">{{ $laporan->no_hp }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-secondary">Lokasi</div>
                <div class="fw-medium">{{ $laporan->gedung->nama_gedung }} — {{ $laporan->lokasi->nama_lokasi }}</div>
                @if ($laporan->latitude && $laporan->longitude)
                    <a href="https://www.google.com/maps?q={{ $laporan->latitude }},{{ $laporan->longitude }}" target="_blank" rel="noopener" class="text-decoration-none">
                        <i class="bi bi-geo-alt me-1"></i>Lihat titik lokasi
                    </a>
                @endif
            </div>
            <div class="col-sm-6">
                <div class="text-secondary">Jenis Kerusakan</div>
                <div class="fw-medium">{{ $laporan->jenisKerusakan->nama_jenis }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-secondary">Fasilitas / Barang</div>
                <div class="fw-medium">{{ $laporan->fasilitas?->nama_fasilitas ?? $laporan->nama_fasilitas_lainnya ?? '—' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-secondary">Tanggal &amp; Waktu Kejadian</div>
                <div class="fw-medium">{{ $laporan->tanggal_kejadian->translatedFormat('d M Y') }}, {{ \Illuminate\Support\Carbon::parse($laporan->waktu_kejadian)->format('H:i') }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-secondary">Dilaporkan Pada</div>
                <div class="fw-medium">{{ $laporan->created_at->translatedFormat('d M Y H:i') }}</div>
            </div>
            <div class="col-12">
                <div class="text-secondary">Deskripsi Kerusakan</div>
                <div>{{ $laporan->deskripsi_kerusakan }}</div>
            </div>
            @if ($laporan->keterangan_tambahan)
                <div class="col-12">
                    <div class="text-secondary">Keterangan Tambahan</div>
                    <div>{{ $laporan->keterangan_tambahan }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach ([['kerusakan', 'Foto Kerusakan', $laporan->fotoKerusakan], ['proses', 'Foto Proses Perbaikan', $laporan->fotoProses], ['selesai', 'Foto Hasil Perbaikan', $laporan->fotoSelesai]] as [$tipe, $judul, $fotos])
    @if ($fotos->isNotEmpty())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium">{{ $judul }}</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($fotos as $foto)
                        @if ($foto->is_video)
                            <video src="{{ $foto->url }}" controls class="rounded-2 border bg-dark" style="width:160px;height:110px;object-fit:cover"></video>
                        @else
                            <a href="{{ $foto->url }}" target="_blank" rel="noopener">
                                <img src="{{ $foto->url }}" alt="{{ $judul }}" class="rounded-2 border" style="width:110px;height:110px;object-fit:cover">
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endforeach

<div class="card border-0 shadow-sm" id="komentar">
    <div class="card-header bg-white fw-medium">Komentar</div>
    <div class="card-body">
        @forelse ($laporan->komentar as $komentar)
            <div class="d-flex gap-2 mb-3">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                    <i class="bi bi-person-fill text-secondary"></i>
                </div>
                <div>
                    <div class="small">
                        <span class="fw-medium">{{ $komentar->user?->name ?? '(pengguna telah dihapus)' }}</span>
                        <span class="badge bg-light text-secondary border ms-1" style="font-size:.65rem">{{ $komentar->user?->role?->display_name }}</span>
                        <span class="text-secondary ms-1">{{ $komentar->created_at->diffForHumans() }}</span>
                    </div>
                    <div>{{ $komentar->pesan }}</div>
                </div>
            </div>
        @empty
            <x-empty-state title="Belum ada komentar" icon="bi-chat-square-text" />
        @endforelse

        <form method="POST" action="{{ route('komentar.store', $laporan) }}#komentar" class="mt-2">
            @csrf
            <div class="input-group">
                <input type="text" name="pesan" class="form-control" placeholder="Tulis komentar..." required maxlength="1000">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
            </div>
        </form>
    </div>
</div>
