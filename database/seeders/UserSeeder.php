<?php

namespace Database\Seeders;

use App\Models\Pelapor;
use App\Models\PetugasHarbang;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Membuat akun contoh untuk masing-masing role.
     * PENTING: ganti seluruh password default ini sebelum aplikasi dipakai di produksi.
     */
    public function run(): void
    {
        $roleSuperAdmin = Role::where('name', Role::SUPER_ADMIN)->first();
        $roleHarbang = Role::where('name', Role::HARBANG)->first();
        $rolePelapor = Role::where('name', Role::PELAPOR)->first();

        // ---- Super Admin ----
        User::updateOrCreate(
            ['email' => 'admin@tarunanusantara.sch.id'],
            [
                'role_id' => $roleSuperAdmin->id,
                'name' => 'Administrator SI-HARBANG',
                'phone' => '081200000001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // ---- Petugas Harbang ----
        $harbangData = [
            ['name' => 'Budi Santoso', 'email' => 'budi.harbang@tarunanusantara.sch.id', 'spesialisasi' => 'Listrik & Elektronik'],
            ['name' => 'Siti Aminah', 'email' => 'siti.harbang@tarunanusantara.sch.id', 'spesialisasi' => 'Bangunan & Sipil'],
        ];

        foreach ($harbangData as $i => $h) {
            $user = User::updateOrCreate(
                ['email' => $h['email']],
                [
                    'role_id' => $roleHarbang->id,
                    'name' => $h['name'],
                    'phone' => '08130000000'.($i + 1),
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            PetugasHarbang::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => '19850101 20100'.($i + 1).' 1 00'.($i + 1),
                    'jabatan' => 'Staf Pemeliharaan Bangunan',
                    'no_hp' => $user->phone,
                    'spesialisasi' => $h['spesialisasi'],
                ]
            );
        }

        // ---- Pelapor ----
        $pelaporData = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad.pelapor@tarunanusantara.sch.id', 'jabatan' => 'Guru Matematika'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.pelapor@tarunanusantara.sch.id', 'jabatan' => 'Pamong Asrama'],
            ['name' => 'Rudi Hartono', 'email' => 'rudi.pelapor@tarunanusantara.sch.id', 'jabatan' => 'Tenaga Kependidikan'],
        ];

        foreach ($pelaporData as $i => $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'role_id' => $rolePelapor->id,
                    'name' => $p['name'],
                    'phone' => '08140000000'.($i + 1),
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            Pelapor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip_nik' => null,
                    'jabatan' => $p['jabatan'],
                    'no_hp' => $user->phone,
                    'unit_kerja' => 'SMA Taruna Nusantara',
                ]
            );
        }
    }
}
