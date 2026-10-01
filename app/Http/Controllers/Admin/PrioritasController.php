<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PrioritasRequest;
use App\Models\AktivitasLog;
use App\Models\Prioritas;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PrioritasController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(Prioritas::query()->orderBy('urutan'))
                ->addColumn('badge', fn (Prioritas $row) => '<span class="badge text-bg-'.$row->warna_badge.'">'.$row->nama.'</span>')
                ->addColumn('aksi', fn (Prioritas $row) => view('admin.prioritas._aksi', ['row' => $row])->render())
                ->rawColumns(['badge', 'aksi'])
                ->make(true);
        }

        return view('admin.prioritas.index', [
            'breadcrumb' => ['Data Master — Prioritas'],
        ]);
    }

    public function store(PrioritasRequest $request): RedirectResponse
    {
        $prioritas = Prioritas::create($request->validated());
        AktivitasLog::catat('menambah data prioritas', deskripsi: $prioritas->nama);

        return back()->with('success', 'Data prioritas berhasil ditambahkan.');
    }

    public function update(PrioritasRequest $request, Prioritas $prioritas): RedirectResponse
    {
        $prioritas->update($request->validated());
        AktivitasLog::catat('mengubah data prioritas', deskripsi: $prioritas->nama);

        return back()->with('success', 'Data prioritas berhasil diperbarui. Perubahan SLA berlaku untuk laporan yang belum diverifikasi.');
    }

    public function destroy(Prioritas $prioritas): RedirectResponse
    {
        try {
            $nama = $prioritas->nama;
            $prioritas->delete();
            AktivitasLog::catat('menghapus data prioritas', deskripsi: $nama);

            return back()->with('success', 'Data prioritas berhasil dihapus.');
        } catch (QueryException) {
            return back()->with('error', 'Prioritas ini tidak dapat dihapus karena masih dipakai pada laporan yang ada.');
        }
    }
}
