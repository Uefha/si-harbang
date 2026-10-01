<?php

namespace App\Exports;

use App\Models\Laporan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private Collection $laporan) {}

    public function collection(): Collection
    {
        return $this->laporan;
    }

    public function headings(): array
    {
        return [
            'Nomor Laporan', 'Tanggal Lapor', 'Pelapor', 'Jabatan', 'Gedung', 'Lokasi',
            'Jenis Kerusakan', 'Fasilitas', 'Deskripsi Kerusakan', 'Prioritas', 'Status',
            'Petugas Harbang', 'Tanggal Kejadian', 'Estimasi Selesai', 'Tanggal Selesai',
            'Terlambat?',
        ];
    }

    public function map($laporan): array
    {
        /** @var Laporan $laporan */
        return [
            $laporan->nomor_laporan,
            $laporan->created_at->format('d/m/Y H:i'),
            $laporan->nama_pelapor,
            $laporan->jabatan,
            $laporan->gedung->nama_gedung,
            $laporan->lokasi->nama_lokasi,
            $laporan->jenisKerusakan->nama_jenis,
            $laporan->fasilitas->nama_fasilitas ?? $laporan->nama_fasilitas_lainnya ?? '-',
            $laporan->deskripsi_kerusakan,
            $laporan->prioritas->nama ?? 'Belum diverifikasi',
            $laporan->status->nama,
            $laporan->petugasHarbang->user->name ?? 'Belum ditugaskan',
            $laporan->tanggal_kejadian->format('d/m/Y'),
            $laporan->estimasi_selesai?->format('d/m/Y') ?? '-',
            $laporan->tanggal_selesai?->format('d/m/Y H:i') ?? '-',
            $laporan->is_terlambat ? 'Ya' : 'Tidak',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DCE6F1'],
            ]],
        ];
    }
}
