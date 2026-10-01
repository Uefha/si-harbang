<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FasilitasRequest;
use App\Models\AktivitasLog;
use App\Models\Fasilitas;
use App\Models\JenisKerusakan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FasilitasController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(Fasilitas::with('jenisKerusakan')->select('fasilitas.*'))
                ->addColumn('jenis', fn (Fasilitas $row) => $row->jenisKerusakan?->nama_jenis ?? '—')
                ->addColumn('status', fn (Fasilitas $row) => $row->is_active
                    ? '<span class="badge text-bg-success">Aktif</span>'
                    : '<span class="badge text-bg-secondary">Nonaktif</span>')
                ->addColumn('aksi', fn (Fasilitas $row) => view('admin.fasilitas._aksi', ['row' => $row])->render())
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('admin.fasilitas.index', [
            'breadcrumb' => ['Data Master — Fasilitas'],
            'jenisKerusakanList' => JenisKerusakan::where('is_active', true)->orderBy('nama_jenis')->get(),
        ]);
    }

    public function store(FasilitasRequest $request): RedirectResponse
    {
        $fasilitas = Fasilitas::create($request->validated());
        AktivitasLog::catat('menambah data fasilitas', deskripsi: $fasilitas->nama_fasilitas);

        return back()->with('success', 'Data fasilitas berhasil ditambahkan.');
    }

    public function update(FasilitasRequest $request, Fasilitas $fasilitas): RedirectResponse
    {
        $fasilitas->update($request->validated());
        AktivitasLog::catat('mengubah data fasilitas', deskripsi: $fasilitas->nama_fasilitas);

        return back()->with('success', 'Data fasilitas berhasil diperbarui.');
    }

    public function destroy(Fasilitas $fasilitas): RedirectResponse
    {
        $nama = $fasilitas->nama_fasilitas;
        $fasilitas->delete();
        AktivitasLog::catat('menghapus data fasilitas', deskripsi: $nama);

        return back()->with('success', 'Data fasilitas berhasil dihapus.');
    }
}
