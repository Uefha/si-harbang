@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <h4 class="fw-semibold mb-3">Pengaturan Sistem</h4>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fs-4 fw-semibold">{{ $totalLaporan }}</div>
                    <div class="small text-secondary">Total laporan (semua waktu)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fs-4 fw-semibold">{{ $totalLaporanHariIni }}</div>
                    <div class="small text-secondary">Laporan hari ini</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fs-5 fw-semibold">{{ $nomorTerakhir ?? '—' }}</div>
                    <div class="small text-secondary">Nomor laporan terakhir hari ini</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-medium">
            <i class="bi bi-telephone me-1"></i>Kontak Admin (Halaman Lupa Kata Sandi)
        </div>
        <div class="card-body">
            <p class="small text-secondary">
                Info ini ditampilkan ke siapa pun yang klik "Lupa kata sandi?" di halaman
                masuk — bisa diubah kapan saja di sini tanpa perlu ubah kode program.
            </p>
            <form method="POST" action="{{ route('admin.pengaturan.kontak-admin') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Nama Admin</label>
                        <input type="text" name="kontak_nama" class="form-control @error('kontak_nama') is-invalid @enderror"
                               value="{{ old('kontak_nama', $kontakNama) }}" required>
                        @error('kontak_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="kontak_telepon" class="form-control @error('kontak_telepon') is-invalid @enderror"
                               value="{{ old('kontak_telepon', $kontakTelepon) }}" placeholder="081234567890" required>
                        @error('kontak_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-medium">Pesan Tambahan (opsional)</label>
                        <textarea name="kontak_pesan" class="form-control @error('kontak_pesan') is-invalid @enderror" rows="2">{{ old('kontak_pesan', $kontakPesan) }}</textarea>
                        @error('kontak_pesan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm mt-3">
                    <i class="bi bi-check-lg me-1"></i>Simpan Kontak
                </button>
            </form>
        </div>
    </div>

    <div class="card border-danger shadow-sm">
        <div class="card-header bg-danger bg-opacity-10 text-danger fw-semibold">
            <i class="bi bi-exclamation-octagon me-1"></i>Zona Berbahaya
        </div>
        <div class="card-body">
            <h6 class="fw-semibold">Reset Nomor Laporan</h6>
            <p class="small text-secondary mb-2">
                Format nomor laporan (<code>HRB-YYYYMMDD-0001</code>) dihasilkan otomatis berdasarkan
                jumlah laporan yang sudah ada. Supaya urutannya bisa mulai lagi dari <strong>0001</strong>,
                seluruh data laporan yang ada — termasuk foto/video, riwayat status, dan komentar — akan
                <strong>dihapus permanen</strong> (bukan sekadar disembunyikan) dan <strong>tidak dapat dikembalikan</strong>.
            </p>
            <p class="small text-secondary">
                Gunakan ini hanya saat masa uji coba selesai dan Anda ingin memulai penomoran dari awal
                sebelum aplikasi benar-benar dipakai — <strong>bukan</strong> untuk operasional sehari-hari.
            </p>

            <form method="POST" action="{{ route('admin.pengaturan.reset-nomor-laporan') }}" id="formReset">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-medium">
                        Ketik <code>RESET SEMUA LAPORAN</code> untuk mengaktifkan tombol hapus:
                    </label>
                    <input type="text" name="konfirmasi" id="inputKonfirmasi" class="form-control @error('konfirmasi') is-invalid @enderror" autocomplete="off">
                    @error('konfirmasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-danger" id="btnReset" disabled>
                    <i class="bi bi-trash3 me-1"></i>Hapus Permanen &amp; Reset Nomor Laporan
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const inputKonfirmasi = document.getElementById('inputKonfirmasi');
const btnReset = document.getElementById('btnReset');
const TEKS_WAJIB = 'RESET SEMUA LAPORAN';

inputKonfirmasi.addEventListener('input', () => {
    btnReset.disabled = inputKonfirmasi.value !== TEKS_WAJIB;
});

document.getElementById('formReset').addEventListener('submit', function (e) {
    if (inputKonfirmasi.value !== TEKS_WAJIB) {
        e.preventDefault();
        return;
    }
    if (!confirm('Ini akan menghapus PERMANEN seluruh data laporan. Benar-benar yakin?')) {
        e.preventDefault();
    }
});
</script>
@endpush
