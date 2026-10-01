<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JenisKerusakanRequest;
use App\Models\AktivitasLog;
use App\Models\JenisKerusakan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class JenisKerusakanController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(JenisKerusakan::query())
                ->addColumn('status', fn (JenisKerusakan $row) => $row->is_active
                    ? '<span class="badge text-bg-success">Aktif</span>'
                    : '<span class="badge text-bg-secondary">Nonaktif</span>')
                ->addColumn('aksi', fn (JenisKerusakan $row) => view('admin.jenis-kerusakan._aksi', ['row' => $row])->render())
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('admin.jenis-kerusakan.index', [
            'breadcrumb' => ['Data Master — Jenis Kerusakan'],
        ]);
    }

    public function store(JenisKerusakanRequest $request): RedirectResponse
    {
        $jenis = JenisKerusakan::create($request->validated());
        AktivitasLog::catat('menambah jenis kerusakan', deskripsi: $jenis->nama_jenis);

        return back()->with('success', 'Jenis kerusakan berhasil ditambahkan.');
    }

    public function update(JenisKerusakanRequest $request, JenisKerusakan $jenisKerusakan): RedirectResponse
    {
        $jenisKerusakan->update($request->validated());
        AktivitasLog::catat('mengubah jenis kerusakan', deskripsi: $jenisKerusakan->nama_jenis);

        return back()->with('success', 'Jenis kerusakan berhasil diperbarui.');
    }

    public function destroy(JenisKerusakan $jenisKerusakan): RedirectResponse
    {
        $nama = $jenisKerusakan->nama_jenis;
        $jenisKerusakan->delete();
        AktivitasLog::catat('menghapus jenis kerusakan', deskripsi: $nama);

        return back()->with('success', 'Jenis kerusakan berhasil dihapus.');
    }
}
