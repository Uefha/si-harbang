<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\LaporanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Laporan\UpdateStatusLaporanRequest;
use App\Http\Requests\Laporan\UploadFotoLaporanRequest;
use App\Http\Requests\Laporan\VerifikasiLaporanRequest;
use App\Models\AktivitasLog;
use App\Models\Gedung;
use App\Models\JenisKerusakan;
use App\Models\Laporan;
use App\Models\PetugasHarbang;
use App\Models\Prioritas;
use App\Models\StatusLaporan;
use App\Services\UploadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    /**
     * Query dasar + filter yang dipakai bersama oleh index(), exportPdf(),
     * dan exportExcel(), supaya hasil unduhan selalu sama persis dengan apa
     * yang sedang dilihat staf di layar (termasuk filter yang aktif).
     */
    private function filteredQuery(Request $request)
    {
        return Laporan::with(['gedung', 'lokasi', 'jenisKerusakan', 'status', 'prioritas', 'petugasHarbang.user', 'pelapor.user'])
            ->when($request->filled('status_id'), fn ($q) => $q->where('status_id', $request->status_id))
            ->when($request->filled('prioritas_id'), fn ($q) => $q->whereIn('prioritas_id', (array) $request->prioritas_id))
            ->when($request->filled('gedung_id'), fn ($q) => $q->where('gedung_id', $request->gedung_id))
            ->when($request->filled('jenis_kerusakan_id'), fn ($q) => $q->where('jenis_kerusakan_id', $request->jenis_kerusakan_id))
            ->when($request->filled('petugas_harbang_id'), fn ($q) => $q->where('petugas_harbang_id', $request->petugas_harbang_id))
            ->when($request->filled('bulan'), fn ($q) => $q->whereMonth('created_at', $request->bulan))
            ->when($request->filled('tahun'), fn ($q) => $q->whereYear('created_at', $request->tahun))
            ->when($request->filled('tanggal_dari'), fn ($q) => $q->whereDate('created_at', '>=', $request->tanggal_dari))
            ->when($request->filled('tanggal_sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->tanggal_sampai))
            ->when($request->boolean('terlambat'), fn ($q) => $q->whereIn('id', $this->idLaporanTerlambat()));
    }

    /**
     * "Terlambat" bukan kolom database (dihitung dari created_at + SLA
     * prioritas lewat accessor is_terlambat di model), jadi untuk keperluan
     * filter di sini dicari dulu ID-nya lewat koleksi non-final yang masih
     * berjumlah wajar untuk sebuah sekolah, baru dipakai whereIn di atas.
     */
    private function idLaporanTerlambat()
    {
        return Laporan::whereHas('status', fn ($q) => $q->where('is_final', false))
            ->whereNotNull('prioritas_id')
            ->with('prioritas', 'status')
            ->get()
            ->filter->is_terlambat
            ->pluck('id');
    }

    /**
     * Daftar SEMUA laporan (Super Admin & Harbang), dengan filter sesuai
     * spesifikasi: Gedung, Jenis Kerusakan, Status, Prioritas, Petugas
     * Harbang, dan rentang tanggal (meng-cover kebutuhan "per hari/minggu/
     * bulan/tahun" tanpa perlu 4 tombol terpisah).
     */
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of($this->filteredQuery($request)->latest())
                ->addColumn('pelapor', fn (Laporan $row) => $row->nama_pelapor)
                ->addColumn('gedung', fn (Laporan $row) => $row->gedung->nama_gedung.' — '.$row->lokasi->nama_lokasi)
                ->addColumn('jenis', fn (Laporan $row) => $row->jenisKerusakan->nama_jenis)
                ->addColumn('petugas', fn (Laporan $row) => $row->petugasHarbang?->user->name ?? '<span class="text-secondary">Belum ditugaskan</span>')
                ->addColumn('status', fn (Laporan $row) => view('components.status-badge', ['status' => $row->status])->render())
                ->addColumn('sla', fn (Laporan $row) => view('components.sla-badge', ['laporan' => $row])->render())
                ->addColumn('tanggal', fn (Laporan $row) => $row->created_at->translatedFormat('d M Y'))
                ->addColumn('aksi', fn (Laporan $row) => '<a href="'.route('laporan.show', $row).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>')
                ->rawColumns(['petugas', 'status', 'sla', 'aksi'])
                ->make(true);
        }

        return view('laporan.index', [
            'gedungList' => Gedung::orderBy('nama_gedung')->get(),
            'jenisKerusakanList' => JenisKerusakan::orderBy('nama_jenis')->get(),
            'prioritasList' => Prioritas::orderBy('urutan')->get(),
            'statusList' => StatusLaporan::orderBy('urutan')->get(),
            // whereHas('user') mengecualikan petugas yang akun user-nya sudah
            // di-soft-delete - tetap ikut sertakan yang nonaktif (bukan cuma
            // is_active) karena ini filter untuk menelusuri laporan LAMA yang
            // mungkin dulu ditangani petugas yang sekarang sudah tidak aktif.
            'petugasList' => PetugasHarbang::with('user')->whereHas('user')->get(),
        ]);
    }

    /**
     * Export daftar laporan (sesuai filter aktif) sebagai PDF cetak.
     */
    public function exportPdf(Request $request): \Illuminate\Http\Response
    {
        $laporan = $this->filteredQuery($request)->latest()->get();

        $pdf = Pdf::loadView('laporan.pdf-list', [
            'laporan' => $laporan,
            'dicetakOleh' => Auth::user(),
            'dicetakPada' => now(),
        ])->setPaper('a4', 'landscape');

        AktivitasLog::catat('mengekspor daftar laporan ke PDF', deskripsi: $laporan->count().' laporan');

        return $pdf->stream('laporan-si-harbang-'.now()->format('Ymd-His').'.pdf');
    }

    /**
     * Export daftar laporan (sesuai filter aktif) sebagai Excel.
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $laporan = $this->filteredQuery($request)->latest()->get();

        AktivitasLog::catat('mengekspor daftar laporan ke Excel', deskripsi: $laporan->count().' laporan');

        return Excel::download(
            new LaporanExport($laporan),
            'laporan-si-harbang-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    /**
     * Cetak satu laporan sebagai dokumen PDF (mis. untuk arsip fisik atau
     * dilampirkan pada serah-terima perbaikan).
     */
    public function pdfDetail(Laporan $laporan): \Illuminate\Http\Response
    {
        $laporan->load([
            'gedung', 'lokasi', 'jenisKerusakan', 'fasilitas', 'prioritas', 'status',
            'petugasHarbang.user', 'pelapor.user', 'fotoKerusakan', 'fotoProses', 'fotoSelesai',
            'riwayatStatus.status', 'riwayatStatus.pengubah',
        ]);

        $pdf = Pdf::loadView('laporan.pdf-detail', [
            'laporan' => $laporan,
            'dicetakOleh' => Auth::user(),
            'dicetakPada' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($laporan->nomor_laporan.'.pdf');
    }

    public function show(Laporan $laporan): View
    {
        $laporan->load([
            'gedung', 'lokasi', 'jenisKerusakan', 'fasilitas', 'prioritas', 'status',
            'petugasHarbang.user', 'pelapor.user', 'fotoKerusakan', 'fotoProses', 'fotoSelesai',
            'riwayatStatus.status', 'riwayatStatus.pengubah', 'komentar.user.role',
        ]);

        return view('laporan.show', [
            'laporan' => $laporan,
            'prioritasList' => Prioritas::orderBy('urutan')->get(),
            'statusList' => StatusLaporan::orderBy('urutan')->get(),
            'petugasList' => PetugasHarbang::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true))->get(),
        ]);
    }

    /**
     * Harbang memverifikasi laporan "Baru": menentukan prioritas resmi
     * (yang menjalankan hitungan SLA), boleh langsung menugaskan petugas
     * atau menugaskannya belakangan lewat form update status.
     */
    public function verifikasi(VerifikasiLaporanRequest $request, Laporan $laporan): RedirectResponse
    {
        abort_if($laporan->status->nama !== StatusLaporan::BARU, 403, 'Laporan ini sudah pernah diverifikasi.');

        $statusDiverifikasiId = StatusLaporan::where('nama', StatusLaporan::DIVERIFIKASI)->value('id');

        $laporan->update([
            'prioritas_id' => $request->prioritas_id,
            'petugas_harbang_id' => $request->petugas_harbang_id,
            'catatan_harbang' => $request->catatan_harbang,
            'estimasi_selesai' => $request->estimasi_selesai,
            'status_id' => $statusDiverifikasiId,
            'tanggal_verifikasi' => now(),
            'verified_by' => Auth::id(),
        ]);

        AktivitasLog::catat('memverifikasi laporan', $laporan, $laporan->nomor_laporan);

        return back()->with('success', 'Laporan berhasil diverifikasi.');
    }

    /**
     * Harbang memperbarui progres penanganan: status, penugasan ulang
     * petugas (opsional), catatan, dan estimasi selesai. Upload foto
     * ditangani terpisah lewat uploadFoto() di bawah.
     */
    public function updateStatus(UpdateStatusLaporanRequest $request, Laporan $laporan): RedirectResponse
    {
        $statusBaru = StatusLaporan::findOrFail($request->status_id);

        $data = [
            'status_id' => $statusBaru->id,
            'catatan_harbang' => $request->catatan_harbang,
            'estimasi_selesai' => $request->estimasi_selesai ?: $laporan->estimasi_selesai,
        ];

        if ($request->filled('petugas_harbang_id')) {
            $data['petugas_harbang_id'] = $request->petugas_harbang_id;
        }

        if ($statusBaru->nama === StatusLaporan::SEDANG_DIKERJAKAN && ! $laporan->tanggal_mulai_perbaikan) {
            $data['tanggal_mulai_perbaikan'] = now();
        }

        if ($statusBaru->is_final && ! $laporan->tanggal_selesai) {
            $data['tanggal_selesai'] = now();
        }

        $laporan->update($data);

        AktivitasLog::catat('mengubah status laporan menjadi "'.$statusBaru->nama.'"', $laporan, $laporan->nomor_laporan);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    /**
     * Unggah foto proses/hasil perbaikan (foto kerusakan awal diunggah
     * pelapor sendiri saat membuat laporan, lihat Pelapor\LaporanController).
     */
    public function uploadFoto(UploadFotoLaporanRequest $request, Laporan $laporan): RedirectResponse
    {
        foreach ($request->file('foto', []) as $file) {
            $path = UploadService::upload($file, 'laporan/'.$laporan->id.'/'.$request->tipe);

            $laporan->fotoLaporan()->create([
                'tipe' => $request->tipe,
                'path' => $path,
                'keterangan' => $request->keterangan,
                'uploaded_by' => Auth::id(),
            ]);
        }

        AktivitasLog::catat('mengunggah foto '.$request->tipe, $laporan, $laporan->nomor_laporan);

        return back()->with('success', 'Foto berhasil diunggah.');
    }

    /**
     * Hapus laporan (soft delete) — dibatasi ke Super Admin lewat middleware
     * role pada grup route, dan tombolnya sendiri hanya tampil untuk
     * super_admin lewat @role() di view.
     */
    public function destroy(Laporan $laporan): RedirectResponse
    {
        // Pembatasan di route group mengizinkan super_admin & harbang, tapi
        // hapus laporan khusus Super Admin — jangan hanya andalkan tombol
        // yang disembunyikan di view (@role di laporan/show.blade.php).
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat menghapus laporan.');

        $nomor = $laporan->nomor_laporan;
        $laporan->delete();

        AktivitasLog::catat('menghapus laporan', deskripsi: $nomor);

        return redirect()->route('laporan.index')->with('success', "Laporan {$nomor} berhasil dihapus.");
    }
}
