<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePetugasHarbangRequest;
use App\Http\Requests\Admin\UpdatePetugasHarbangRequest;
use App\Models\AktivitasLog;
use App\Models\PetugasHarbang;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PetugasHarbangController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(PetugasHarbang::with('user')->whereHas('user')->select('petugas_harbang.*'))
                ->addColumn('name', fn (PetugasHarbang $row) => $row->user->name)
                ->addColumn('email', fn (PetugasHarbang $row) => $row->user->email)
                ->addColumn('status', fn (PetugasHarbang $row) => $row->user->is_active
                    ? '<span class="badge text-bg-success">Aktif</span>'
                    : '<span class="badge text-bg-secondary">Nonaktif</span>')
                ->addColumn('aksi', fn (PetugasHarbang $row) => view('admin.master-data._aksi', [
                    'row' => $row,
                    'editRoute' => 'javascript:void(0)',
                    'editAttrs' => 'onclick="editPetugas('.$row->id.')"',
                    'deleteRoute' => route('admin.petugas-harbang.destroy', $row),
                ])->render())
                ->rawColumns(['status', 'aksi'])
                ->make(true);
        }

        return view('admin.petugas-harbang.index');
    }

    public function store(StorePetugasHarbangRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'role_id' => Role::where('name', Role::HARBANG)->value('id'),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->no_hp,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            PetugasHarbang::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
                'spesialisasi' => $request->spesialisasi,
            ]);
        });

        AktivitasLog::catat('menambah akun petugas harbang baru');

        return back()->with('success', 'Akun petugas Harbang berhasil ditambahkan.');
    }

    public function update(UpdatePetugasHarbangRequest $request, PetugasHarbang $petugasHarbang): RedirectResponse
    {
        DB::transaction(function () use ($request, $petugasHarbang) {
            $userData = $request->safe()->only(['name', 'email']);
            $userData['phone'] = $request->no_hp;
            $userData['is_active'] = $request->boolean('is_active');
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $petugasHarbang->user->update($userData);

            $petugasHarbang->update($request->safe()->only(['nip', 'jabatan', 'no_hp', 'spesialisasi']));
        });

        AktivitasLog::catat('mengubah data petugas harbang: '.$petugasHarbang->user->email);

        return back()->with('success', 'Data petugas Harbang berhasil diperbarui.');
    }

    public function destroy(PetugasHarbang $petugasHarbang): RedirectResponse
    {
        try {
            if (! $petugasHarbang->user) {
                // Data tidak konsisten (user sudah tidak ada) - bersihkan baris yatim ini.
                $petugasHarbang->delete();
                AktivitasLog::catat('menghapus data petugas harbang yatim (akun user sudah tidak ada)');

                return back()->with('success', 'Data petugas (tanpa akun) berhasil dibersihkan.');
            }

            if ($petugasHarbang->laporan()->exists()) {
                // Riwayat siapa-menangani-apa harus tetap utuh (termasuk laporan yang
                // sudah selesai) - kalau baris petugas_harbang ini dihapus,
                // petugas_harbang_id di laporan lama otomatis ikut kosong (nullOnDelete),
                // jadi cukup nonaktifkan akunnya saja, jangan dihapus.
                $petugasHarbang->user->update(['is_active' => false]);
                AktivitasLog::catat('menonaktifkan akun petugas harbang (punya riwayat penanganan laporan): '.$petugasHarbang->user->email);

                return back()->with('success', 'Petugas ini memiliki riwayat penanganan laporan, sehingga akun dinonaktifkan (bukan dihapus) agar riwayat tetap utuh. Statusnya kini "Nonaktif" di daftar ini.');
            }

            $email = $petugasHarbang->user->email;
            $petugasHarbang->user->delete();
            $petugasHarbang->delete();
            AktivitasLog::catat('menghapus akun petugas harbang: '.$email);

            return back()->with('success', 'Akun petugas Harbang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return back()->with('error', 'Data petugas ini tidak dapat dihapus karena masih terhubung dengan data lain di sistem. Coba nonaktifkan akunnya lewat tombol Ubah, bukan Hapus.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Terjadi kesalahan saat menghapus data petugas: '.$e->getMessage());
        }
    }
}
