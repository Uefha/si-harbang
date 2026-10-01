<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\JenisKerusakan;
use App\Models\Laporan;
use App\Models\Prioritas;
use App\Models\Role;
use App\Models\StatusLaporan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Konten dashboard berbeda per role. Kartu ringkasan & grafik Chart.js
     * (per bulan/gedung/kategori/prioritas) memakai query agregat langsung
     * ke tabel laporan.
     */
    public function index(): View
    {
        $user = Auth::user();

        return match ($user->role?->name) {
            Role::SUPER_ADMIN => view('dashboard.admin', $this->adminData()),
            Role::HARBANG => view('dashboard.harbang', $this->harbangData()),
            Role::PELAPOR => view('dashboard.pelapor', $this->pelaporData()),
            default => abort(403),
        };
    }

    private function adminData(): array
    {
        $statusId = StatusLaporan::pluck('id', 'nama');
        $prioritasUrgentIds = Prioritas::whereIn('nama', ['Tinggi', 'Darurat'])->pluck('id')->all();

        return [
            'totalLaporan' => Laporan::count(),
            'laporanBaru' => Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::BARU))->count(),
            'sedangDiverifikasi' => Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::DIVERIFIKASI))->count(),
            'sedangDikerjakan' => Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::SEDANG_DIKERJAKAN))->count(),
            'menungguSparepart' => Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::MENUNGGU_MATERIAL))->count(),
            'selesai' => Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::SELESAI))->count(),
            'ditolak' => Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::DITOLAK))->count(),
            'prioritasTinggi' => Laporan::whereIn('prioritas_id', $prioritasUrgentIds)->count(),
            'terlambat' => Laporan::whereHas('status', fn ($q) => $q->where('is_final', false))->get()->filter->is_terlambat->count(),
            'laporanTerbaru' => Laporan::with(['gedung', 'status', 'prioritas'])->latest()->take(5)->get(),

            // ---- ID untuk kartu ringkasan yang bisa diklik ke Semua Laporan ----
            'statusId' => $statusId,
            'prioritasUrgentIds' => $prioritasUrgentIds,

            // ---- Data 4 grafik Chart.js ----
            'chartBulan' => $this->chartPerBulan(),
            'chartGedung' => $this->chartPerGedung(),
            'chartJenis' => $this->chartPerJenisKerusakan(),
            'chartPrioritas' => $this->chartPerPrioritas(),
        ];
    }

    /**
     * Tren jumlah laporan 12 bulan terakhir, termasuk bulan tanpa laporan
     * (ditampilkan sebagai 0, bukan hilang dari grafik).
     */
    private function chartPerBulan(): array
    {
        $bulanan = collect(range(11, 0))->map(function (int $i) {
            $bulan = now()->subMonths($i);

            return [
                'label' => $bulan->translatedFormat('M Y'),
                'total' => Laporan::whereYear('created_at', $bulan->year)
                    ->whereMonth('created_at', $bulan->month)
                    ->count(),
            ];
        });

        return [
            'labels' => $bulanan->pluck('label'),
            'data' => $bulanan->pluck('total'),
        ];
    }

    private function chartPerGedung(): array
    {
        $data = Gedung::withCount('laporan')->get()
            ->filter(fn (Gedung $g) => $g->laporan_count > 0)
            ->sortByDesc('laporan_count')
            ->take(8)
            ->values();

        return [
            'labels' => $data->pluck('nama_gedung'),
            'data' => $data->pluck('laporan_count'),
        ];
    }

    private function chartPerJenisKerusakan(): array
    {
        $data = JenisKerusakan::withCount('laporan')->get()
            ->filter(fn (JenisKerusakan $j) => $j->laporan_count > 0)
            ->sortByDesc('laporan_count')
            ->take(8)
            ->values();

        return [
            'labels' => $data->pluck('nama_jenis'),
            'data' => $data->pluck('laporan_count'),
        ];
    }

    /**
     * Diurutkan sesuai `urutan` di data master (Rendah→Darurat), bukan
     * jumlah terbanyak, supaya bentuk grafik konsisten setiap saat dilihat.
     * Warna badge disertakan supaya grafik & badge di seluruh aplikasi selaras.
     */
    private function chartPerPrioritas(): array
    {
        $data = Prioritas::orderBy('urutan')->withCount('laporan')->get();

        return [
            'labels' => $data->pluck('nama'),
            'data' => $data->pluck('laporan_count'),
            'warna' => $data->pluck('warna_badge'),
        ];
    }

    private function harbangData(): array
    {
        $petugas = Auth::user()->petugasHarbang;

        return [
            'belumDiverifikasi' => Laporan::whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::BARU))->count(),
            'ditugaskanKeSaya' => $petugas ? Laporan::where('petugas_harbang_id', $petugas->id)->whereHas('status', fn ($q) => $q->where('is_final', false))->count() : 0,
            'terlambat' => Laporan::whereHas('status', fn ($q) => $q->where('is_final', false))->get()->filter->is_terlambat->count(),
            'selesaiHariIni' => Laporan::whereDate('tanggal_selesai', today())->count(),
            'antrianVerifikasi' => Laporan::with(['gedung', 'lokasi', 'jenisKerusakan'])
                ->whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::BARU))
                ->latest()->take(5)->get(),
            'statusIdBaru' => StatusLaporan::where('nama', StatusLaporan::BARU)->value('id'),
            'statusIdSelesai' => StatusLaporan::where('nama', StatusLaporan::SELESAI)->value('id'),
            'petugasId' => $petugas?->id,
        ];
    }

    private function pelaporData(): array
    {
        $pelapor = Auth::user()->pelapor;

        $laporanSaya = $pelapor ? Laporan::where('pelapor_id', $pelapor->id) : Laporan::whereRaw('1 = 0');

        return [
            'totalLaporanSaya' => (clone $laporanSaya)->count(),
            'sedangDiproses' => (clone $laporanSaya)->whereHas('status', fn ($q) => $q->where('is_final', false))->count(),
            'selesai' => (clone $laporanSaya)->whereHas('status', fn ($q) => $q->where('nama', StatusLaporan::SELESAI))->count(),
            'laporanTerbaru' => (clone $laporanSaya)->with(['status', 'gedung'])->latest()->take(5)->get(),
        ];
    }
}
