<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $default = [
            Pengaturan::KONTAK_ADMIN_NAMA => 'Admin SI-HARBANG',
            Pengaturan::KONTAK_ADMIN_TELEPON => '081200000001',
            Pengaturan::KONTAK_ADMIN_PESAN => 'Lupa kata sandi? Hubungi admin di bawah ini untuk dibantu atur ulang.',
        ];

        foreach ($default as $key => $value) {
            Pengaturan::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
