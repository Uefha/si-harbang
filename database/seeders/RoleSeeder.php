<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => Role::SUPER_ADMIN, 'display_name' => 'Super Admin', 'description' => 'Akses penuh ke seluruh sistem'],
            ['name' => Role::HARBANG, 'display_name' => 'Petugas Harbang', 'description' => 'Menerima & menangani laporan kerusakan'],
            ['name' => Role::PELAPOR, 'display_name' => 'Pelapor', 'description' => 'Guru, pamong, tenaga kependidikan, dan petugas'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
