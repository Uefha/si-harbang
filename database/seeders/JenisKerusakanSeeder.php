<?php

namespace Database\Seeders;

use App\Models\JenisKerusakan;
use Illuminate\Database\Seeder;

class JenisKerusakanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_jenis' => 'Bangunan', 'icon' => 'bi-building'],
            ['nama_jenis' => 'Listrik', 'icon' => 'bi-lightning-charge'],
            ['nama_jenis' => 'Air', 'icon' => 'bi-droplet'],
            ['nama_jenis' => 'Sanitasi', 'icon' => 'bi-water'],
            ['nama_jenis' => 'Atap', 'icon' => 'bi-house'],
            ['nama_jenis' => 'Pintu', 'icon' => 'bi-door-closed'],
            ['nama_jenis' => 'Jendela', 'icon' => 'bi-window'],
            ['nama_jenis' => 'AC', 'icon' => 'bi-snow'],
            ['nama_jenis' => 'CCTV', 'icon' => 'bi-camera-video'],
            ['nama_jenis' => 'Jaringan Internet', 'icon' => 'bi-wifi'],
            ['nama_jenis' => 'Furniture', 'icon' => 'bi-box-seam'],
            ['nama_jenis' => 'Meja', 'icon' => 'bi-table'],
            ['nama_jenis' => 'Kursi', 'icon' => 'bi-menu-button-wide'],
            ['nama_jenis' => 'Lampu', 'icon' => 'bi-lightbulb'],
            ['nama_jenis' => 'Plafon', 'icon' => 'bi-layout-text-window'],
            ['nama_jenis' => 'Cat Dinding', 'icon' => 'bi-paint-bucket'],
            ['nama_jenis' => 'Pagar', 'icon' => 'bi-bricks'],
            ['nama_jenis' => 'Jalan', 'icon' => 'bi-signpost'],
            ['nama_jenis' => 'Saluran Air', 'icon' => 'bi-moisture'],
            ['nama_jenis' => 'Lainnya', 'icon' => 'bi-three-dots'],
        ];

        foreach ($data as $item) {
            JenisKerusakan::updateOrCreate(['nama_jenis' => $item['nama_jenis']], $item);
        }
    }
}
