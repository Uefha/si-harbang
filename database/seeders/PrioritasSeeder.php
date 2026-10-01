<?php

namespace Database\Seeders;

use App\Models\Prioritas;
use Illuminate\Database\Seeder;

class PrioritasSeeder extends Seeder
{
    public function run(): void
    {
        // sla_hari mengikuti ketentuan SLA pada spesifikasi
        $data = [
            ['nama' => 'Rendah', 'sla_hari' => 14, 'warna_badge' => 'success', 'urutan' => 1],
            ['nama' => 'Sedang', 'sla_hari' => 7, 'warna_badge' => 'info', 'urutan' => 2],
            ['nama' => 'Tinggi', 'sla_hari' => 3, 'warna_badge' => 'warning', 'urutan' => 3],
            ['nama' => 'Darurat', 'sla_hari' => 1, 'warna_badge' => 'danger', 'urutan' => 4],
        ];

        foreach ($data as $item) {
            Prioritas::updateOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
