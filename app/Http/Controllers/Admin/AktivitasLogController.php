<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AktivitasLogController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(AktivitasLog::with(['user', 'laporan'])->latest('created_at'))
                ->addColumn('user', fn (AktivitasLog $row) => $row->user?->name ?? 'Sistem')
                ->addColumn('laporan', fn (AktivitasLog $row) => $row->laporan?->nomor_laporan ?? '-')
                ->addColumn('waktu', fn (AktivitasLog $row) => $row->created_at->translatedFormat('d M Y H:i'))
                ->rawColumns([])
                ->make(true);
        }

        return view('admin.aktivitas-log.index');
    }
}
