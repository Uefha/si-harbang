<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            StatusLaporanSeeder::class,
            PrioritasSeeder::class,
            GedungSeeder::class,
            LokasiSeeder::class,
            JenisKerusakanSeeder::class,
            FasilitasSeeder::class,
            UserSeeder::class,
            PengaturanSeeder::class,
        ]);
    }
}
