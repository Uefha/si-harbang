@extends('layouts.app')

@section('title', $laporan->nomor_laporan)

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h4 class="fw-semibold mb-0">Detail Laporan</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.pdf-detail', $laporan) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-file-earmark-pdf me-1"></i>Cetak PDF
            </a>
            @role('super_admin')
                <form method="POST" action="{{ route('laporan.destroy', $laporan) }}" class="form-hapus"
                      data-confirm-message="Hapus laporan {{ $laporan->nomor_laporan }}? Tindakan ini tidak dapat dibatalkan.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Hapus Laporan</button>
                </form>
            @endrole
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            @include('laporan._detail_main')
        </div>

        <div class="col-lg-4">
            {{-- Panel Aksi Harbang --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-medium">Aksi Penanganan</div>
                <div class="card-body">
                    @if ($laporan->status->nama === \App\Models\StatusLaporan::BARU)
                        <p class="small text-secondary">Laporan ini belum diverifikasi. Tentukan prioritas resmi untuk memulai penanganan dan mengaktifkan hitungan SLA.</p>
                        <form method="POST" action="{{ route('laporan.verifikasi', $laporan) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-2">
                                <label class="form-label small fw-medium">Prioritas Resmi <span class="text-danger">*</span></label>
                                <select name="prioritas_id" class="form-select form-select-sm" required>
                                    <option value="">Pilih prioritas</option>
                                    @foreach ($prioritasList as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} (SLA {{ $p->sla_hari }} hari)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-medium">Tugaskan Petugas</label>
                                <select name="petugas_harbang_id" class="form-select form-select-sm">
                                    <option value="">— Belum ditugaskan —</option>
                                    @foreach ($petugasList as $p)
                                        <option value="{{ $p->id }}">{{ $p->user?->name ?? '(akun tidak ditemukan)' }} ({{ $p->spesialisasi ?? 'Umum' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-medium">Estimasi Selesai</label>
                                <input type="date" name="estimasi_selesai" class="form-control form-control-sm">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Catatan</label>
                                <textarea name="catatan_harbang" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Verifikasi Laporan</button>
                        </form>
                    @elseif (! $laporan->status->is_final)
                        <form method="POST" action="{{ route('laporan.update-status', $laporan) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-2">
                                <label class="form-label small fw-medium">Ubah Status <span class="text-danger">*</span></label>
                                <select name="status_id" class="form-select form-select-sm" required>
                                    @foreach ($statusList->where('nama', '!=', \App\Models\StatusLaporan::BARU) as $s)
                                        <option value="{{ $s->id }}" @selected($s->id === $laporan->status_id)>{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-medium">Petugas</label>
                                <select name="petugas_harbang_id" class="form-select form-select-sm">
                                    <option value="">— Tidak diubah —</option>
                                    @foreach ($petugasList as $p)
                                        <option value="{{ $p->id }}" @selected($p->id === $laporan->petugas_harbang_id)>{{ $p->user?->name ?? '(akun tidak ditemukan)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-medium">Estimasi Selesai</label>
                                <input type="date" name="estimasi_selesai" class="form-control form-control-sm" value="{{ $laporan->estimasi_selesai?->format('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Catatan</label>
                                <textarea name="catatan_harbang" class="form-control form-control-sm" rows="2">{{ $laporan->catatan_harbang }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Simpan Perubahan Status</button>
                        </form>

                        <hr>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalFotoProses">
                                <i class="bi bi-camera me-1"></i>Unggah Foto Proses
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalFotoSelesai">
                                <i class="bi bi-camera me-1"></i>Unggah Foto Hasil
                            </button>
                        </div>
                    @else
                        <div class="alert alert-{{ $laporan->status->warna_badge }} small mb-0">
                            Laporan ini sudah berstatus akhir (<strong>{{ $laporan->status->nama }}</strong>) dan tidak dapat diubah lagi.
                        </div>
                    @endif
                </div>
            </div>

            @include('laporan._detail_sidebar')
        </div>
    </div>

    {{-- Modal upload foto proses/selesai --}}
    @foreach (['proses' => 'Unggah Foto Proses Perbaikan', 'selesai' => 'Unggah Foto Hasil Perbaikan'] as $tipe => $judul)
        <div class="modal fade" id="modalFoto{{ ucfirst($tipe) }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('laporan.upload-foto', $laporan) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tipe" value="{{ $tipe }}">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $judul }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Pilih Foto/Video (bisa lebih dari satu, maks. 5, maks. 20MB per file)</label>
                                <input type="file" name="foto[]" class="form-control" accept="image/*,video/*" capture="environment" multiple required>
                                <div class="form-text">Di HP, ini akan menawarkan pilihan langsung memotret/merekam dengan kamera atau memilih dari galeri.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Unggah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
