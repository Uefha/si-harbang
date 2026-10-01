<?php

return [
    // Prefix nomor laporan otomatis: {prefix}-YYYYMMDD-0001
    'nomor_prefix' => env('HARBANG_NOMOR_PREFIX', 'HRB'),

    // Batas SLA default (hari) — nilai final tetap dikelola lewat tabel `prioritas`
    // agar Super Admin bisa mengubahnya dari UI tanpa deploy ulang.
    'sla_default' => [
        'darurat' => (int) env('HARBANG_SLA_DARURAT', 1),
        'tinggi' => (int) env('HARBANG_SLA_TINGGI', 3),
        'sedang' => (int) env('HARBANG_SLA_SEDANG', 7),
        'rendah' => (int) env('HARBANG_SLA_RENDAH', 14),
    ],

    // Path penyimpanan foto di storage/app/public
    'foto_path' => 'laporan-foto',
];
