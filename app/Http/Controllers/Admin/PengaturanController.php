<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\FotoLaporan;
use App\Models\Laporan;
use App\Models\Pengaturan;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        return view('admin.pengaturan.index', [
            'breadcrumb' => ['Pengaturan Sistem'],
            'totalLaporan' => Laporan::withTrashed()->count(),
            'totalLaporanHariIni' => Laporan::withTrashed()->whereDate('created_at', today())->count(),
            'nomorTerakhir' => Laporan::withTrashed()->whereDate('created_at', today())->orderByDesc('id')->value('nomor_laporan'),
            'kontakNama' => Pengaturan::get(Pengaturan::KONTAK_ADMIN_NAMA),
            'kontakTelepon' => Pengaturan::get(Pengaturan::KONTAK_ADMIN_TELEPON),
            'kontakPesan' => Pengaturan::get(Pengaturan::KONTAK_ADMIN_PESAN),
        ]);
    }

    /**
     * Kontak admin yang ditampilkan di halaman "Lupa Kata Sandi" - dibuat
     * bisa diubah lewat sini (bukan hardcode di kode/env) supaya Super Admin
     * bisa menggantinya sendiri kapan saja, mis. kalau nomornya berganti.
     */
    public function updateKontakAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kontak_nama' => ['required', 'string', 'max:255'],
            'kontak_telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'kontak_pesan' => ['nullable', 'string', 'max:500'],
        ], [
            'kontak_telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, +, dan -.',
        ]);

        Pengaturan::set(Pengaturan::KONTAK_ADMIN_NAMA, $data['kontak_nama']);
        Pengaturan::set(Pengaturan::KONTAK_ADMIN_TELEPON, $data['kontak_telepon']);
        Pengaturan::set(Pengaturan::KONTAK_ADMIN_PESAN, $data['kontak_pesan'] ?? null);

        AktivitasLog::catat('mengubah kontak admin untuk halaman lupa kata sandi');

        return redirect()->route('admin.pengaturan.index')->with('success', 'Kontak admin berhasil diperbarui.');
    }

    /**
     * Menghapus PERMANEN seluruh data laporan (beserta foto/video, riwayat
     * status, dan komentar lewat cascade) sehingga penomoran otomatis
     * (HRB-YYYYMMDD-0001) kembali mulai dari 0001 di hari berikutnya laporan
     * dibuat. Dipakai sebelum aplikasi benar-benar dipakai (selesai masa uji
     * coba), BUKAN untuk operasional harian.
     */
    public function resetNomorLaporan(Request $request): RedirectResponse
    {
        $request->validate([
            'konfirmasi' => ['required', 'in:RESET SEMUA LAPORAN'],
        ], [
            'konfirmasi.required' => 'Ketik teks konfirmasi persis seperti yang diminta.',
            'konfirmasi.in' => 'Teks konfirmasi tidak sesuai. Ketik persis: RESET SEMUA LAPORAN',
        ]);

        $jumlah = Laporan::withTrashed()->count();

        // Hapus file foto/video fisik dari storage supaya tidak jadi sampah
        FotoLaporan::chunk(200, function ($rows) {
            foreach ($rows as $foto) {
                UploadService::delete($foto->path);
            }
        });
        UploadService::deleteDirectory('laporan');

        // forceDelete melewati SoftDeletes -> baris benar-benar hilang, dan
        // foto_laporan/riwayat_status/komentar_laporan ikut terhapus lewat
        // cascadeOnDelete di migration masing-masing.
        Laporan::withTrashed()->each(fn (Laporan $l) => $l->forceDelete());

        AktivitasLog::catat(
            'RESET seluruh data laporan',
            deskripsi: "{$jumlah} laporan dihapus permanen oleh ".$request->user()->name.'. Penomoran otomatis akan mulai dari 0001 lagi.'
        );

        return redirect()->route('admin.pengaturan.index')
            ->with('success', "Berhasil: {$jumlah} laporan (beserta foto, riwayat, dan komentar) telah dihapus permanen. Nomor laporan berikutnya akan dimulai dari 0001.");
    }
}
