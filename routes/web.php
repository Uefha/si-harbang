<?php

use App\Http\Controllers\Admin\AktivitasLogController;
use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\Admin\GedungController;
use App\Http\Controllers\Admin\JenisKerusakanController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Admin\PelaporController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PetugasHarbangController;
use App\Http\Controllers\Admin\PrioritasController;
use App\Http\Controllers\Admin\StatusLaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Laporan\KomentarController;
use App\Http\Controllers\Laporan\LaporanController;
use App\Http\Controllers\Pelapor\LaporanController as PelaporLaporanController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SI-HARBANG
|--------------------------------------------------------------------------
| Fase 1-3 selesai: auth, dashboard per role, dan CRUD Data Master +
| manajemen akun (User/Pelapor/Petugas Harbang) sudah aktif di bawah.
| Fase 4 (alur Laporan) masih kerangka — lihat baris yang dikomentari
| pada grup "laporan." dan "lapor." di bawah, serta README.md.
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// ================= Auth =================
// Tidak ada route "register": akun pelapor & harbang dibuat oleh Super Admin
// lewat menu Kelola User, bukan lewat pendaftaran mandiri. Lihat routes/auth.php.
require __DIR__.'/auth.php';

Route::middleware(['auth', 'nocache'])->group(function () {

    // ---------- Dashboard (semua role, konten disesuaikan lewat controller) ----------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------- Khusus Super Admin ----------
    Route::middleware('role:'.Role::SUPER_ADMIN)
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', UserController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['users' => 'user']);

            Route::resource('pelapor', PelaporController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['pelapor' => 'pelapor']);

            Route::resource('petugas-harbang', PetugasHarbangController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['petugas-harbang' => 'petugasHarbang']);

            Route::resource('gedung', GedungController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['gedung' => 'gedung']);

            Route::resource('lokasi', LokasiController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['lokasi' => 'lokasi']);

            Route::resource('jenis-kerusakan', JenisKerusakanController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['jenis-kerusakan' => 'jenisKerusakan']);

            Route::resource('fasilitas', FasilitasController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['fasilitas' => 'fasilitas']);

            Route::resource('prioritas', PrioritasController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['prioritas' => 'prioritas']);

            Route::resource('status', StatusLaporanController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['status' => 'status']);

            Route::get('aktivitas-log', [AktivitasLogController::class, 'index'])->name('aktivitas-log');

            Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
            Route::post('pengaturan/reset-nomor-laporan', [PengaturanController::class, 'resetNomorLaporan'])->name('pengaturan.reset-nomor-laporan');
            Route::post('pengaturan/kontak-admin', [PengaturanController::class, 'updateKontakAdmin'])->name('pengaturan.kontak-admin');
        });

    // ---------- Super Admin & Harbang: kelola semua laporan ----------
    Route::middleware('role:'.Role::SUPER_ADMIN.','.Role::HARBANG)
        ->prefix('laporan')
        ->name('laporan.')
        ->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/export/pdf', [LaporanController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/export/excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
            Route::get('/{laporan}', [LaporanController::class, 'show'])->name('show');
            Route::get('/{laporan}/pdf', [LaporanController::class, 'pdfDetail'])->name('pdf-detail');
            Route::patch('/{laporan}/verifikasi', [LaporanController::class, 'verifikasi'])->name('verifikasi');
            Route::patch('/{laporan}/status', [LaporanController::class, 'updateStatus'])->name('update-status');
            Route::post('/{laporan}/foto', [LaporanController::class, 'uploadFoto'])->name('upload-foto');
            Route::delete('/{laporan}', [LaporanController::class, 'destroy'])->name('destroy');
        });

    // ---------- Khusus Pelapor ----------
    Route::middleware('role:'.Role::PELAPOR)
        ->prefix('lapor')
        ->name('lapor.')
        ->group(function () {
            Route::get('/', [PelaporLaporanController::class, 'index'])->name('index');
            Route::get('/buat', [PelaporLaporanController::class, 'create'])->name('create');
            Route::post('/', [PelaporLaporanController::class, 'store'])->name('store');
            Route::get('/{laporan}', [PelaporLaporanController::class, 'show'])->name('show');
        });

    // ---------- Bersama: komentar (staf & pelapor pemilik laporan) ----------
    Route::post('laporan/{laporan}/komentar', [KomentarController::class, 'store'])->name('komentar.store');
    // Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
});
