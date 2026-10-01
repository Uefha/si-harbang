<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePelaporRequest;
use App\Http\Requests\Admin\UpdatePelaporRequest;
use App\Models\AktivitasLog;
use App\Models\Pelapor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PelaporController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            // whereHas('user') otomatis mengecualikan akun yang sudah di-soft-delete
            return DataTables::of(Pelapor::with('user')->whereHas('user')->select('pelapor.*'))
                ->addColumn('name', fn (Pelapor $row) => $row->user->name)
                ->addColumn('email', fn (Pelapor $row) => $row->user->email)
                ->addColumn('status', fn (Pelapor $row) => $row->user->is_active
                    ? '<span class="badge text-bg-success">Aktif</span>'
                    : '<span class="badge text-bg-secondary">Nonaktif</span>')
                ->addColumn('aksi', fn (Pelapor $row) => view('admin.master-data._aksi', [
                    'row' => $row,
                    'editRoute' => 'javascript:void(0)',
                    'editAttrs' => 'onclick="editPelapor('.$row->id.')"',
                    'deleteRoute' => route('admin.pelapor.destroy', $row),
                ])->render())
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('admin.pelapor.index');
    }

    public function store(StorePelaporRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'role_id' => Role::where('name', Role::PELAPOR)->value('id'),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->no_hp,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            Pelapor::create([
                'user_id' => $user->id,
                'nip_nik' => $request->nip_nik,
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
                'unit_kerja' => $request->unit_kerja,
            ]);
        });

        AktivitasLog::catat('menambah akun pelapor baru');

        return back()->with('success', 'Akun pelapor berhasil ditambahkan.');
    }

    public function update(UpdatePelaporRequest $request, Pelapor $pelapor): RedirectResponse
    {
        DB::transaction(function () use ($request, $pelapor) {
            $userData = $request->safe()->only(['name', 'email']);
            $userData['phone'] = $request->no_hp;
            $userData['is_active'] = $request->boolean('is_active');
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $pelapor->user->update($userData);

            $pelapor->update($request->safe()->only(['nip_nik', 'jabatan', 'no_hp', 'unit_kerja']));
        });

        AktivitasLog::catat('mengubah data pelapor: '.$pelapor->user->email);

        return back()->with('success', 'Data pelapor berhasil diperbarui.');
    }

    public function destroy(Pelapor $pelapor): RedirectResponse
    {
        try {
            if (! $pelapor->user) {
                // Data tidak konsisten (user sudah tidak ada) - bersihkan baris yatim ini.
                // Tetap bisa gagal kalau ada laporan yang masih mengacu ke sini (FK restrict),
                // makanya tetap di dalam try/catch yang sama.
                $pelapor->delete();
                AktivitasLog::catat('menghapus data pelapor yatim (akun user sudah tidak ada)');

                return back()->with('success', 'Data pelapor (tanpa akun) berhasil dibersihkan.');
            }

            if ($pelapor->laporan()->exists()) {
                // Riwayat laporan harus tetap utuh, jadi akun HANYA dinonaktifkan,
                // TIDAK di-soft-delete - kalau ikut di-soft-delete, baris ini malah
                // hilang dari daftar (whereHas('user') otomatis menyembunyikan user
                // yang soft-deleted), padahal pesannya bilang "dinonaktifkan".
                $pelapor->user->update(['is_active' => false]);
                AktivitasLog::catat('menonaktifkan akun pelapor (punya riwayat laporan): '.$pelapor->user->email);

                return back()->with('success', 'Pelapor memiliki riwayat laporan, sehingga akun dinonaktifkan (bukan dihapus) agar riwayat tetap utuh. Statusnya kini "Nonaktif" di daftar ini.');
            }

            $email = $pelapor->user->email;
            $pelapor->user->delete();
            $pelapor->delete();
            AktivitasLog::catat('menghapus akun pelapor: '.$email);

            return back()->with('success', 'Akun pelapor berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return back()->with('error', 'Data pelapor ini tidak dapat dihapus karena masih terhubung dengan data lain di sistem. Coba nonaktifkan akunnya lewat tombol Ubah, bukan Hapus.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Terjadi kesalahan saat menghapus data pelapor: '.$e->getMessage());
        }
    }
}
