<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\JenisKerusakan;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'AC' => ['Unit AC Ruang Kelas', 'Unit AC Kantor', 'Unit AC Asrama'],
            'Lampu' => ['Lampu TL Ruang Kelas', 'Lampu Sorot Lapangan', 'Lampu Koridor'],
            'Meja' => ['Meja Kelas', 'Meja Kantor'],
            'Kursi' => ['Kursi Kelas', 'Kursi Kantor', 'Kursi Aula'],
            'CCTV' => ['Kamera CCTV Koridor', 'Kamera CCTV Gerbang'],
            'Jaringan Internet' => ['Access Point WiFi', 'Kabel LAN'],
        ];

        foreach ($map as $jenisNama => $items) {
            $jenis = JenisKerusakan::where('nama_jenis', $jenisNama)->first();

            foreach ($items as $nama) {
                Fasilitas::updateOrCreate(
                    ['nama_fasilitas' => $nama],
                    ['jenis_kerusakan_id' => $jenis?->id]
                );
            }
        }
    }
}
