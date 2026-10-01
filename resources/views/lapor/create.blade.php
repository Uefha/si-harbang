@extends('layouts.app')

@section('title', 'Buat Laporan Kerusakan')

@section('content')
    <x-breadcrumb :items="['Buat Laporan']" />
    <h4 class="fw-semibold mb-3">Buat Laporan Kerusakan</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-medium mb-1"><i class="bi bi-exclamation-circle me-1"></i>Periksa kembali isian Anda:</div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lapor.store') }}" enctype="multipart/form-data" id="formLaporan">
        @csrf

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium">1. Data Pelapor</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelapor" class="form-control" value="{{ old('nama_pelapor', auth()->user()->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $pelapor?->jabatan) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Nomor HP <span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $pelapor?->no_hp) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Nomor Induk / NIP / NIK</label>
                        <input type="text" name="nip_nik" class="form-control" value="{{ old('nip_nik', $pelapor?->nip_nik) }}" placeholder="Opsional">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium">2. Lokasi &amp; Objek Kerusakan</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Gedung <span class="text-danger">*</span></label>
                        <select name="gedung_id" class="form-select" required>
                            <option value="">Pilih gedung...</option>
                            @foreach ($gedungList as $g)
                                <option value="{{ $g->id }}" @selected(old('gedung_id') == $g->id)>{{ $g->nama_gedung }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Lokasi <span class="text-danger">*</span></label>
                        <select name="lokasi_id" class="form-select" required>
                            <option value="">Pilih lokasi...</option>
                            @foreach ($lokasiList as $l)
                                <option value="{{ $l->id }}" @selected(old('lokasi_id') == $l->id)>{{ $l->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Jenis Kerusakan <span class="text-danger">*</span></label>
                        <select name="jenis_kerusakan_id" class="form-select" required>
                            <option value="">Pilih jenis kerusakan...</option>
                            @foreach ($jenisKerusakanList as $j)
                                <option value="{{ $j->id }}" @selected(old('jenis_kerusakan_id') == $j->id)>{{ $j->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Nama Barang / Fasilitas</label>
                        <select name="fasilitas_id" id="fasilitas_id" class="form-select">
                            <option value="">— Tidak ada di daftar —</option>
                            @foreach ($fasilitasList->groupBy(fn($f) => $f->jenisKerusakan?->nama_jenis ?? 'Lainnya') as $grup => $items)
                                <optgroup label="{{ $grup }}">
                                    @foreach ($items as $f)
                                        <option value="{{ $f->id }}" @selected(old('fasilitas_id') == $f->id)>{{ $f->nama_fasilitas }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                            <option value="__lainnya__" @selected(old('nama_fasilitas_lainnya'))>Lainnya (isi manual)</option>
                        </select>
                        <input type="text" name="nama_fasilitas_lainnya" id="nama_fasilitas_lainnya"
                               class="form-control mt-2 {{ old('nama_fasilitas_lainnya') ? '' : 'd-none' }}"
                               placeholder="Tulis nama barang/fasilitas" value="{{ old('nama_fasilitas_lainnya') }}">
                        <div class="form-text">Pilih dari daftar, atau pilih "Lainnya" untuk isi manual.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium">3. Detail Kerusakan</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-medium">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_kerusakan" class="form-control" rows="3" required placeholder="Jelaskan kondisi kerusakan secara singkat...">{{ old('deskripsi_kerusakan') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-medium">Tingkat Urgensi <span class="text-danger">*</span></label>
                        <select name="tingkat_urgensi_pelapor" class="form-select" required>
                            <option value="rendah" @selected(old('tingkat_urgensi_pelapor') == 'rendah')>Rendah</option>
                            <option value="sedang" @selected(old('tingkat_urgensi_pelapor', 'sedang') == 'sedang')>Sedang</option>
                            <option value="tinggi" @selected(old('tingkat_urgensi_pelapor') == 'tinggi')>Tinggi</option>
                            <option value="darurat" @selected(old('tingkat_urgensi_pelapor') == 'darurat')>Darurat</option>
                        </select>
                        <div class="form-text">Penilaian awal Anda — prioritas resmi ditentukan Harbang saat verifikasi.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-medium">Tanggal Kejadian <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_kejadian" class="form-control" max="{{ date('Y-m-d') }}" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-medium">Waktu Kejadian <span class="text-danger">*</span></label>
                        <input type="time" name="waktu_kejadian" class="form-control" value="{{ old('waktu_kejadian', date('H:i')) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-medium">Titik Lokasi (Opsional)</label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLokasiSaya">
                                <i class="bi bi-geo-alt me-1"></i>Gunakan Lokasi Saya
                            </button>
                            <span id="lokasiSayaInfo" class="small text-secondary"></span>
                        </div>
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-medium">Keterangan Tambahan</label>
                        <textarea name="keterangan_tambahan" class="form-control" rows="2">{{ old('keterangan_tambahan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium">4. Foto / Video Kerusakan <span class="text-danger">*</span></div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <input type="file" name="foto[]" id="fotoInput" class="form-control" accept="image/*,video/*" multiple required>
                    <button type="button" class="btn btn-outline-primary text-nowrap" id="btnAmbilKamera">
                        <i class="bi bi-camera me-1"></i>Ambil dari Kamera
                    </button>
                    <input type="file" id="kameraFallbackInput" class="d-none" accept="image/*,video/*" capture="environment">
                </div>
                <div class="form-text">Unggah 1–5 foto/video (JPG/PNG/WEBP atau MP4/MOV/WEBM, maks 20MB per file). Tombol "Ambil dari Kamera" membuka kamera langsung di halaman ini untuk memotret/merekam di lokasi kejadian.</div>
                <div class="row g-2 mt-1" id="fotoPreview"></div>
            </div>
        </div>

        <div class="modal fade" id="modalKamera" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark">
                    <div class="modal-header border-0">
                        <h6 class="modal-title text-white">Kamera</h6>
                        <button type="button" class="btn-close btn-close-white" id="btnTutupKamera"></button>
                    </div>
                    <div class="modal-body p-0 text-center position-relative">
                        <video id="kameraPreview" autoplay playsinline muted class="w-100" style="max-height:60vh;background:#000"></video>
                        <div id="kameraRecTimer" class="position-absolute top-0 start-0 m-2 badge text-bg-danger d-none">
                            <i class="bi bi-record-fill"></i> <span id="kameraRecTimerText">00:00</span>
                        </div>
                        <canvas id="kameraCanvas" class="d-none"></canvas>
                    </div>
                    <div class="modal-footer border-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light" id="btnJepretFoto"><i class="bi bi-camera-fill me-1"></i>Ambil Foto</button>
                        <button type="button" class="btn btn-danger" id="btnRekamVideo"><i class="bi bi-record-circle me-1"></i>Rekam Video</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Kirim Laporan</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
// Toggle input manual "Lainnya" untuk fasilitas
const fasilitasSelect = document.getElementById('fasilitas_id');
const fasilitasLainnya = document.getElementById('nama_fasilitas_lainnya');
fasilitasSelect.addEventListener('change', function () {
    fasilitasLainnya.classList.toggle('d-none', this.value !== '__lainnya__');
});
document.getElementById('formLaporan').addEventListener('submit', function () {
    if (fasilitasSelect.value === '__lainnya__') {
        fasilitasSelect.value = '';
    }
});

// Lokasi via Geolocation API (tanpa perlu API key Google Maps)
document.getElementById('btnLokasiSaya').addEventListener('click', function () {
    const info = document.getElementById('lokasiSayaInfo');
    if (!navigator.geolocation) {
        info.textContent = 'Browser Anda tidak mendukung deteksi lokasi.';
        return;
    }
    info.textContent = 'Mendeteksi lokasi...';
    navigator.geolocation.getCurrentPosition(function (pos) {
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        info.innerHTML = `Lokasi tersimpan (${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)}) — <a href="https://www.google.com/maps?q=${pos.coords.latitude},${pos.coords.longitude}" target="_blank" rel="noopener">lihat di peta</a>`;
    }, function () {
        info.textContent = 'Gagal mendeteksi lokasi. Pastikan izin lokasi diaktifkan.';
    });
});

// Foto/video: gabungkan pilihan dari "galeri" (multi-select) dan "kamera"
// (capture langsung) ke satu daftar yang sama, dengan preview & tombol hapus.
let fotoFiles = [];
const fotoInput = document.getElementById('fotoInput');
const preview = document.getElementById('fotoPreview');

function renderPreview() {
    preview.innerHTML = '';
    fotoFiles.forEach((file, idx) => {
        const col = document.createElement('div');
        col.className = 'col-4 col-md-2 position-relative';

        const media = file.type.startsWith('video/')
            ? Object.assign(document.createElement('video'), { controls: true, muted: true })
            : document.createElement('img');
        media.className = 'img-fluid rounded border';
        media.style.aspectRatio = '1';
        media.style.objectFit = 'cover';
        media.style.width = '100%';
        media.src = URL.createObjectURL(file);

        const btnHapus = document.createElement('button');
        btnHapus.type = 'button';
        btnHapus.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1 py-0 px-1';
        btnHapus.innerHTML = '<i class="bi bi-x"></i>';
        btnHapus.onclick = () => { fotoFiles.splice(idx, 1); syncInputAndRender(); };

        col.appendChild(media);
        col.appendChild(btnHapus);
        preview.appendChild(col);
    });
}

// Input file asli hanya bisa dikirim sebagai satu FileList, jadi setiap kali
// daftar berubah (tambah dari galeri/kamera, atau hapus), kita bangun ulang
// FileList gabungannya lewat DataTransfer sebelum form dikirim.
function syncInputAndRender() {
    const dt = new DataTransfer();
    fotoFiles.slice(0, 5).forEach(f => dt.items.add(f));
    fotoFiles = fotoFiles.slice(0, 5);
    fotoInput.files = dt.files;
    renderPreview();
}

fotoInput.addEventListener('change', function () {
    fotoFiles = fotoFiles.concat([...this.files]);
    syncInputAndRender();
});

// ---- Akses kamera langsung (getUserMedia) ----
// Ini membuka stream kamera perangkat sungguhan di dalam halaman, BUKAN
// membuka file picker OS. Kalau browser/perangkat tidak mendukung (mis.
// diakses lewat http:// bukan https:// di server produksi), otomatis
// jatuh ke input file dengan atribut "capture" sebagai cadangan.
const modalKameraEl = document.getElementById('modalKamera');
const modalKamera = new bootstrap.Modal(modalKameraEl);
const videoPreview = document.getElementById('kameraPreview');
const canvasKamera = document.getElementById('kameraCanvas');
const btnJepretFoto = document.getElementById('btnJepretFoto');
const btnRekamVideo = document.getElementById('btnRekamVideo');
const recTimerBadge = document.getElementById('kameraRecTimer');
const recTimerText = document.getElementById('kameraRecTimerText');
const kameraFallbackInput = document.getElementById('kameraFallbackInput');

let kameraStream = null;
let mediaRecorder = null;
let rekamChunks = [];
let rekamMulai = null;
let rekamIntervalId = null;

async function bukaKamera() {
    if (!navigator.mediaDevices?.getUserMedia) {
        // Browser tidak mengizinkan akses kamera langsung (getUserMedia) -
        // ini HAMPIR SELALU karena halaman diakses lewat HTTP biasa, bukan
        // HTTPS/localhost. Kamera browser hanya bisa diakses dari koneksi
        // aman. Jelaskan ke pengguna, jangan diam-diam pindah ke file picker.
        const alasan = !window.isSecureContext
            ? 'Ini karena halaman diakses lewat koneksi HTTP biasa (bukan HTTPS) — browser memblokir akses kamera langsung di koneksi yang tidak aman, walau dari HP di jaringan WiFi yang sama dengan server.'
            : 'Perangkat/browser ini sepertinya tidak mendukung akses kamera langsung dari halaman web.';
        alert('Tidak bisa membuka kamera langsung. ' + alasan + ' Silakan pilih lewat file browser (biasanya tetap ada pilihan "Kamera" di sana).');
        kameraFallbackInput.click();
        return;
    }
    try {
        kameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' } },
            audio: true,
        });
        videoPreview.srcObject = kameraStream;
        modalKamera.show();

        // Safari/iOS umumnya tidak mendukung MediaRecorder sama sekali, atau
        // hanya mendukung mp4 (bukan webm). Sembunyikan tombol rekam kalau
        // memang tidak ada format yang didukung, daripada tombolnya error
        // saat ditekan - ambil foto tetap selalu berfungsi di semua browser.
        const formatVideoDidukung = typeof MediaRecorder !== 'undefined'
            && (MediaRecorder.isTypeSupported('video/webm;codecs=vp9')
                || MediaRecorder.isTypeSupported('video/webm')
                || MediaRecorder.isTypeSupported('video/mp4'));
        btnRekamVideo.classList.toggle('d-none', !formatVideoDidukung);
    } catch (err) {
        alert('Tidak bisa mengakses kamera (' + err.message + '). Pastikan Anda mengizinkan akses kamera di browser, lalu coba lagi. Sebagai cadangan, pilih file secara manual.');
        kameraFallbackInput.click();
    }
}

function tutupKamera() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
    }
    kameraStream?.getTracks().forEach(track => track.stop());
    kameraStream = null;
    videoPreview.srcObject = null;
    clearInterval(rekamIntervalId);
    recTimerBadge.classList.add('d-none');
    btnRekamVideo.innerHTML = '<i class="bi bi-record-circle me-1"></i>Rekam Video';
    btnRekamVideo.classList.replace('btn-light', 'btn-danger');
    btnJepretFoto.disabled = false;
    modalKamera.hide();
}

document.getElementById('btnAmbilKamera').addEventListener('click', bukaKamera);
document.getElementById('btnTutupKamera').addEventListener('click', tutupKamera);

btnJepretFoto.addEventListener('click', () => {
    canvasKamera.width = videoPreview.videoWidth;
    canvasKamera.height = videoPreview.videoHeight;
    canvasKamera.getContext('2d').drawImage(videoPreview, 0, 0);
    canvasKamera.toBlob(blob => {
        const file = new File([blob], `kamera-${Date.now()}.jpg`, { type: 'image/jpeg' });
        fotoFiles.push(file);
        syncInputAndRender();
        tutupKamera();
    }, 'image/jpeg', 0.9);
});

btnRekamVideo.addEventListener('click', () => {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        return;
    }

    rekamChunks = [];
    const kandidatMime = ['video/webm;codecs=vp9', 'video/webm', 'video/mp4'];
    const mimeType = kandidatMime.find(m => MediaRecorder.isTypeSupported(m)) || '';
    mediaRecorder = mimeType ? new MediaRecorder(kameraStream, { mimeType }) : new MediaRecorder(kameraStream);

    mediaRecorder.ondataavailable = e => { if (e.data.size > 0) rekamChunks.push(e.data); };
    mediaRecorder.onstop = () => {
        const tipeAkhir = mediaRecorder.mimeType || 'video/webm';
        const ekstensi = tipeAkhir.includes('mp4') ? 'mp4' : 'webm';
        const blob = new Blob(rekamChunks, { type: tipeAkhir });
        const file = new File([blob], `kamera-${Date.now()}.${ekstensi}`, { type: tipeAkhir });
        fotoFiles.push(file);
        syncInputAndRender();
        tutupKamera();
    };

    mediaRecorder.start();
    rekamMulai = Date.now();
    recTimerBadge.classList.remove('d-none');
    rekamIntervalId = setInterval(() => {
        const detik = Math.floor((Date.now() - rekamMulai) / 1000);
        recTimerText.textContent = String(Math.floor(detik / 60)).padStart(2, '0') + ':' + String(detik % 60).padStart(2, '0');
    }, 500);

    btnJepretFoto.disabled = true;
    btnRekamVideo.innerHTML = '<i class="bi bi-stop-fill me-1"></i>Stop &amp; Simpan';
    btnRekamVideo.classList.replace('btn-danger', 'btn-light');
});

// Cadangan: kalau getUserMedia gagal/tidak didukung, hasil dari input file
// biasa (dengan atribut capture) tetap masuk ke daftar yang sama.
kameraFallbackInput.addEventListener('change', function () {
    if (this.files[0]) {
        fotoFiles.push(this.files[0]);
        syncInputAndRender();
    }
    kameraFallbackInput.value = '';
});
</script>
@endpush
