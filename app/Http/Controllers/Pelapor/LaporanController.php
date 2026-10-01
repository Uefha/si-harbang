<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Laporan\StoreLaporanRequest;
use App\Models\AktivitasLog;
use App\Models\Fasilitas;
use App\Models\Gedung;
use App\Models\JenisKerusakan;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\StatusLaporan;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $pelaporId = Auth::user()->pelapor?->id;

        if ($request->ajax()) {
            return DataTables::of(
                    Laporan::with(['gedung', 'lokasi', 'status', 'prioritas'])
                        ->where('pelapor_id', $pelaporId)
                        ->latest()
                )
                ->addColumn('gedung', fn (Laporan $row) => $row->gedung->nama_gedung.' — '.$row->lokasi->nama_lokasi)
                ->addColumn('status', fn (Laporan $row) => view('components.status-badge', ['status' => $row->status])->render())
                ->addColumn('sla', fn (Laporan $row) => view('components.sla-badge', ['laporan' => $row])->render())
                ->addColumn('tanggal', fn (Laporan $row) => $row->created_at->translatedFormat('d M Y'))
                ->addColumn('aksi', fn (Laporan $row) => '<a href="'.route('lapor.show', $row).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>')
                ->rawColumns(['status', 'sla', 'aksi'])
                ->make(true);
        }

        return view('lapor.index', ['breadcrumb' => ['Laporan Saya']]);
    }

    public function create(): View
    {
        $pelapor = Auth::user()->pelapor;

        return view('lapor.create', [
            'breadcrumb' => ['Laporan Saya' => route('lapor.index'), 'Buat Laporan'],
            'pelapor' => $pelapor,
            'gedungList' => Gedung::where('is_active', true)->orderBy('nama_gedung')->get(),
            'lokasiList' => Lokasi::where('is_active', true)->orderBy('nama_lokasi')->get(),
            'jenisKerusakanList' => JenisKerusakan::where('is_active', true)->orderBy('nama_jenis')->get(),
            'fasilitasList' => Fasilitas::where('is_active', true)->orderBy('nama_fasilitas')->get(['id', 'nama_fasilitas', 'jenis_kerusakan_id']),
        ]);
    }

    public function store(StoreLaporanRequest $request): RedirectResponse
    {
        $pelapor = Auth::user()->pelapor;

        // Jaring pengaman di server terhadap kirim dobel (tombol diklik
        // berkali-kali, koneksi lambat, dsb.) - kalau pelapor yang sama baru
        // saja (20 detik terakhir) mengirim laporan dengan isian identik
        // (gedung/lokasi/jenis/deskripsi sama persis), anggap itu klik ganda
        // dari laporan yang sama, bukan laporan baru - arahkan ke laporan
        // yang sudah dibuat itu saja, jangan buat duplikatnya.
        $kemungkinanDobel = Laporan::where('pelapor_id', $pelapor->id)
            ->where('gedung_id', $request->gedung_id)
            ->where('lokasi_id', $request->lokasi_id)
            ->where('jenis_kerusakan_id', $request->jenis_kerusakan_id)
            ->where('deskripsi_kerusakan', $request->deskripsi_kerusakan)
            ->where('created_at', '>=', now()->subSeconds(20))
            ->latest()
            ->first();

        if ($kemungkinanDobel) {
            return redirect()->route('lapor.show', $kemungkinanDobel)
                ->with('success', "Laporan sudah terkirim sebelumnya dengan nomor {$kemungkinanDobel->nomor_laporan}.");
        }

        $statusBaru = StatusLaporan::where('nama', StatusLaporan::BARU)->firstOrFail();

        $laporan = Laporan::create([
            ...$request->safe()->except('foto'),
            'pelapor_id' => $pelapor->id,
            'status_id' => $statusBaru->id,
        ]);

        foreach ($request->file('foto', []) as $file) {
            $path = UploadService::upload($file, 'laporan/'.$laporan->id.'/kerusakan');

            $laporan->fotoLaporan()->create([
                'tipe' => 'kerusakan',
                'path' => $path,
                'uploaded_by' => Auth::id(),
            ]);
        }

        AktivitasLog::catat('membuat laporan baru', $laporan);

        return redirect()->route('lapor.show', $laporan)
            ->with('success', "Laporan berhasil dikirim dengan nomor {$laporan->nomor_laporan}.");
    }

    public function show(Laporan $laporan): View
    {
        abort_unless($laporan->pelapor_id === Auth::user()->pelapor?->id, 403);

        $laporan->load([
            'gedung', 'lokasi', 'jenisKerusakan', 'fasilitas', 'prioritas', 'status',
            'petugasHarbang.user', 'fotoKerusakan', 'fotoProses', 'fotoSelesai',
            'riwayatStatus.status', 'komentar.user.role',
        ]);

        return view('lapor.show', [
            'breadcrumb' => ['Laporan Saya' => route('lapor.index'), $laporan->nomor_laporan],
            'laporan' => $laporan,
        ]);
    }
}
