<?php

namespace Database\Seeders;

use App\Models\Gedung;
use Illuminate\Database\Seeder;

class GedungSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Graha Putra', 'Graha Putri', 'Gedung Akademik', 'Ruang Kelas',
            'Laboratorium', 'Masjid', 'GOR', 'Aula', 'Kantor', 'Dapur',
            'Klinik', 'Perpustakaan', 'Lapangan', 'Pos Jaga',
        ];

        foreach ($data as $i => $nama) {
            Gedung::updateOrCreate(
                ['nama_gedung' => $nama],
                ['kode_gedung' => 'GD-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)]
            );
        }
    }
}
