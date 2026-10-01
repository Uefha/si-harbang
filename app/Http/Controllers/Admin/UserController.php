<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\AktivitasLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Daftar SEMUA akun (lintas role). Pembuatan akun baru di sini khusus
     * untuk role Super Admin — akun Pelapor/Harbang dibuat lewat menu
     * masing-masing karena butuh data profil tambahan sekaligus.
     */
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->ajax()) {
            return DataTables::of(User::with('role')->select('users.*'))
                ->addColumn('role_badge', function (User $row) {
                    $color = match ($row->role?->name) {
                        Role::SUPER_ADMIN => 'primary',
                        Role::HARBANG => 'info',
                        default => 'secondary',
                    };

                    return '<span class="badge text-bg-'.$color.'">'.$row->role?->display_name.'</span>';
                })
                ->addColumn('status', fn (User $row) => $row->is_active
                    ? '<span class="badge text-bg-success">Aktif</span>'
                    : '<span class="badge text-bg-secondary">Nonaktif</span>')
                ->addColumn('aksi', function (User $row) {
                    $canDelete = $row->id !== auth()->id();

                    return view('admin.users._aksi', ['row' => $row, 'canDelete' => $canDelete])->render();
                })
                ->rawColumns(['role_badge', 'status', 'aksi'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $superAdminRoleId = Role::where('name', Role::SUPER_ADMIN)->value('id');

        User::create([
            'role_id' => $superAdminRoleId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        AktivitasLog::catat('menambah akun Super Admin baru');

        return back()->with('success', 'Akun Super Admin berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'phone']);
        $data['is_active'] = $request->boolean('is_active');

        // Jangan biarkan Super Admin menonaktifkan akunnya sendiri secara tidak sengaja
        if ($user->id === auth()->id()) {
            $data['is_active'] = true;
        }

        // Kosongkan field kata sandi di form berarti tidak diubah.
        $ubahSandi = $request->filled('password');
        if ($ubahSandi) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        AktivitasLog::catat(
            $ubahSandi ? 'mengubah data & kata sandi akun user: '.$user->email : 'mengubah data akun user: '.$user->email
        );

        return back()->with('success', $ubahSandi
            ? 'Data akun & kata sandi berhasil diperbarui.'
            : 'Data akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->isSuperAdmin() && User::whereHas('role', fn ($q) => $q->where('name', Role::SUPER_ADMIN))->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus satu-satunya akun Super Admin yang tersisa.');
        }

        $email = $user->email;
        $user->delete();
        AktivitasLog::catat('menghapus akun user: '.$email);

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
