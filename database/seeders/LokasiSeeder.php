<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Lantai 1', 'Lantai 2', 'Lantai 3', 'Ruangan', 'Koridor',
            'Toilet', 'Gudang', 'Halaman', 'Parkiran', 'Tangga',
        ];

        foreach ($data as $nama) {
            Lokasi::updateOrCreate(['nama_lokasi' => $nama]);
        }
    }
}
