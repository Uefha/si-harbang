<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LokasiRequest;
use App\Models\AktivitasLog;
use App\Models\Lokasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LokasiController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(Lokasi::query())
                ->addColumn('status', fn (Lokasi $row) => $row->is_active
                    ? '<span class="badge text-bg-success">Aktif</span>'
                    : '<span class="badge text-bg-secondary">Nonaktif</span>')
                ->addColumn('aksi', fn (Lokasi $row) => view('admin.lokasi._aksi', ['row' => $row])->render())
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('admin.lokasi.index', [
            'breadcrumb' => ['Data Master — Lokasi'],
        ]);
    }

    public function store(LokasiRequest $request): RedirectResponse
    {
        $lokasi = Lokasi::create($request->validated());
        AktivitasLog::catat('menambah data lokasi', deskripsi: $lokasi->nama_lokasi);

        return back()->with('success', 'Data lokasi berhasil ditambahkan.');
    }

    public function update(LokasiRequest $request, Lokasi $lokasi): RedirectResponse
    {
        $lokasi->update($request->validated());
        AktivitasLog::catat('mengubah data lokasi', deskripsi: $lokasi->nama_lokasi);

        return back()->with('success', 'Data lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi): RedirectResponse
    {
        $nama = $lokasi->nama_lokasi;
        $lokasi->delete();
        AktivitasLog::catat('menghapus data lokasi', deskripsi: $nama);

        return back()->with('success', 'Data lokasi berhasil dihapus.');
    }
}
