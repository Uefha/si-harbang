<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Laporan\KomentarRequest;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(KomentarRequest $request, Laporan $laporan): RedirectResponse
    {
        $user = Auth::user();

        // Pelapor hanya boleh berkomentar di laporan miliknya sendiri.
        if ($user->isPelapor()) {
            abort_unless($laporan->pelapor_id === $user->pelapor?->id, 403);
        }

        $laporan->komentar()->create([
            'user_id' => $user->id,
            'pesan' => $request->pesan,
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }
}
