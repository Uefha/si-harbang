<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StatusLaporanRequest;
use App\Models\AktivitasLog;
use App\Models\StatusLaporan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class StatusLaporanController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(StatusLaporan::query()->orderBy('urutan'))
                ->addColumn('badge', fn (StatusLaporan $row) => '<span class="badge text-bg-'.$row->warna_badge.'">'.$row->nama.'</span>')
                ->addColumn('final', fn (StatusLaporan $row) => $row->is_final
                    ? '<span class="badge text-bg-dark">Status Akhir</span>'
                    : '<span class="text-secondary small">—</span>')
                ->addColumn('aksi', fn (StatusLaporan $row) => view('admin.status._aksi', ['row' => $row])->render())
                ->rawColumns(['badge', 'final', 'aksi'])
                ->make(true);
        }

        return view('admin.status.index', [
            'breadcrumb' => ['Data Master — Status Laporan'],
        ]);
    }

    public function store(StatusLaporanRequest $request): RedirectResponse
    {
        $status = StatusLaporan::create($request->validated());
        AktivitasLog::catat('menambah status laporan', deskripsi: $status->nama);

        return back()->with('success', 'Status laporan berhasil ditambahkan.');
    }

    public function update(StatusLaporanRequest $request, StatusLaporan $status): RedirectResponse
    {
        $status->update($request->validated());
        AktivitasLog::catat('mengubah status laporan', deskripsi: $status->nama);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function destroy(StatusLaporan $status): RedirectResponse
    {
        try {
            $nama = $status->nama;
            $status->delete();
            AktivitasLog::catat('menghapus status laporan', deskripsi: $nama);

            return back()->with('success', 'Status laporan berhasil dihapus.');
        } catch (QueryException) {
            return back()->with('error', 'Status ini tidak dapat dihapus karena masih dipakai pada laporan yang ada.');
        }
    }
}
