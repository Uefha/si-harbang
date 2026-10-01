<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Laporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan';

    protected $fillable = [
        'nomor_laporan', 'pelapor_id', 'nama_pelapor', 'nip_nik', 'jabatan', 'no_hp',
        'gedung_id', 'lokasi_id', 'jenis_kerusakan_id', 'fasilitas_id', 'nama_fasilitas_lainnya',
        'deskripsi_kerusakan', 'tingkat_urgensi_pelapor', 'prioritas_id',
        'status_id', 'petugas_harbang_id',
        'tanggal_kejadian', 'waktu_kejadian', 'latitude', 'longitude',
        'keterangan_tambahan', 'catatan_harbang', 'estimasi_selesai',
        'tanggal_verifikasi', 'tanggal_mulai_perbaikan', 'tanggal_selesai',
        'verified_by', 'qr_code_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'estimasi_selesai' => 'date',
            'tanggal_verifikasi' => 'datetime',
            'tanggal_mulai_perbaikan' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    // ================= Relasi =================

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(Pelapor::class);
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class);
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function jenisKerusakan(): BelongsTo
    {
        return $this->belongsTo(JenisKerusakan::class);
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class);
    }

    public function prioritas(): BelongsTo
    {
        return $this->belongsTo(Prioritas::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusLaporan::class, 'status_id');
    }

    public function petugasHarbang(): BelongsTo
    {
        return $this->belongsTo(PetugasHarbang::class, 'petugas_harbang_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function fotoLaporan(): HasMany
    {
        return $this->hasMany(FotoLaporan::class);
    }

    public function fotoKerusakan(): HasMany
    {
        return $this->fotoLaporan()->where('tipe', 'kerusakan');
    }

    public function fotoProses(): HasMany
    {
        return $this->fotoLaporan()->where('tipe', 'proses');
    }

    public function fotoSelesai(): HasMany
    {
        return $this->fotoLaporan()->where('tipe', 'selesai');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class)->latest('created_at');
    }

    public function komentar(): HasMany
    {
        return $this->hasMany(KomentarLaporan::class)->oldest();
    }

    public function aktivitasLog(): HasMany
    {
        return $this->hasMany(AktivitasLog::class);
    }

    // ================= Accessor turunan =================

    /**
     * Tenggat waktu SLA = created_at + jumlah hari SLA dari prioritas resmi.
     * Selama prioritas resmi belum ditetapkan Harbang, SLA belum berjalan.
     */
    public function getSlaDeadlineAttribute(): ?Carbon
    {
        if (! $this->prioritas) {
            return null;
        }

        return $this->created_at->copy()->addDays($this->prioritas->sla_hari);
    }

    /**
     * True jika laporan sudah melewati SLA dan belum berstatus final (Selesai/Ditolak).
     */
    public function getIsTerlambatAttribute(): bool
    {
        if (! $this->sla_deadline || $this->status?->is_final) {
            return false;
        }

        return now()->greaterThan($this->sla_deadline);
    }

    /**
     * Durasi penanganan sejak laporan dibuat sampai selesai, format "X hari Y jam".
     * Null selama laporan belum berstatus selesai.
     */
    public function getDurasiPenangananAttribute(): ?string
    {
        if (! $this->tanggal_selesai) {
            return null;
        }

        $menit = $this->created_at->diffInMinutes($this->tanggal_selesai);
        $hari = intdiv($menit, 1440);
        $jam = intdiv($menit % 1440, 60);

        return $hari > 0 ? "{$hari} hari {$jam} jam" : "{$jam} jam";
    }

    // ================= Boot hooks =================

    protected static function booted(): void
    {
        // Nomor laporan otomatis: HRB-YYYYMMDD-0001
        static::creating(function (Laporan $laporan) {
            if (empty($laporan->nomor_laporan)) {
                $laporan->nomor_laporan = static::generateNomorLaporan();
            }
        });

        // Catat status awal ("Baru") ke riwayat_status begitu laporan dibuat
        static::created(function (Laporan $laporan) {
            $laporan->riwayatStatus()->create([
                'status_id' => $laporan->status_id,
                'changed_by' => Auth::id(),
                'catatan' => 'Laporan dibuat oleh pelapor.',
            ]);
        });

        // Catat setiap perubahan status ke tabel riwayat_status secara otomatis
        static::updated(function (Laporan $laporan) {
            if ($laporan->wasChanged('status_id')) {
                $laporan->riwayatStatus()->create([
                    'status_id' => $laporan->status_id,
                    'changed_by' => Auth::id(),
                    'catatan' => $laporan->catatan_harbang,
                ]);
            }
        });
    }

    public static function generateNomorLaporan(): string
    {
        $prefix = config('harbang.nomor_prefix', 'HRB');
        $tanggal = now()->format('Ymd');

        return DB::transaction(function () use ($prefix, $tanggal) {
            $count = static::withTrashed()
                ->whereDate('created_at', now()->toDateString())
                ->lockForUpdate()
                ->count();

            $urutan = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);

            return "{$prefix}-{$tanggal}-{$urutan}";
        });
    }
}
