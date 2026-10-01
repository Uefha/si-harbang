<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GedungRequest;
use App\Models\AktivitasLog;
use App\Models\Gedung;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GedungController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(Gedung::query())
                ->addColumn('status', fn (Gedung $row) => $row->is_active
                    ? '<span class="badge text-bg-success">Aktif</span>'
                    : '<span class="badge text-bg-secondary">Nonaktif</span>')
                ->addColumn('aksi', fn (Gedung $row) => view('admin.gedung._aksi', ['row' => $row])->render())
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('admin.gedung.index', [
            'breadcrumb' => ['Data Master — Gedung'],
        ]);
    }

    public function store(GedungRequest $request): RedirectResponse
    {
        $gedung = Gedung::create($request->validated());
        AktivitasLog::catat('menambah data gedung', deskripsi: $gedung->nama_gedung);

        return back()->with('success', 'Data gedung berhasil ditambahkan.');
    }

    public function update(GedungRequest $request, Gedung $gedung): RedirectResponse
    {
        $gedung->update($request->validated());
        AktivitasLog::catat('mengubah data gedung', deskripsi: $gedung->nama_gedung);

        return back()->with('success', 'Data gedung berhasil diperbarui.');
    }

    public function destroy(Gedung $gedung): RedirectResponse
    {
        $nama = $gedung->nama_gedung;
        $gedung->delete();
        AktivitasLog::catat('menghapus data gedung', deskripsi: $nama);

        return back()->with('success', 'Data gedung berhasil dihapus.');
    }
}
