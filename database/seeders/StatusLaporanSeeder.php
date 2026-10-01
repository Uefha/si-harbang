<?php

namespace Database\Seeders;

use App\Models\StatusLaporan;
use Illuminate\Database\Seeder;

class StatusLaporanSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['nama' => StatusLaporan::BARU, 'warna_badge' => 'secondary', 'urutan' => 1, 'is_final' => false],
            ['nama' => StatusLaporan::DIVERIFIKASI, 'warna_badge' => 'info', 'urutan' => 2, 'is_final' => false],
            ['nama' => StatusLaporan::MENUNGGU_PERBAIKAN, 'warna_badge' => 'warning', 'urutan' => 3, 'is_final' => false],
            ['nama' => StatusLaporan::SEDANG_DIKERJAKAN, 'warna_badge' => 'primary', 'urutan' => 4, 'is_final' => false],
            ['nama' => StatusLaporan::MENUNGGU_MATERIAL, 'warna_badge' => 'dark', 'urutan' => 5, 'is_final' => false],
            ['nama' => StatusLaporan::SELESAI, 'warna_badge' => 'success', 'urutan' => 6, 'is_final' => true],
            ['nama' => StatusLaporan::DITOLAK, 'warna_badge' => 'danger', 'urutan' => 7, 'is_final' => true],
        ];

        foreach ($statuses as $status) {
            StatusLaporan::updateOrCreate(['nama' => $status['nama']], $status);
        }
    }
}
