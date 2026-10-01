<?php

namespace App\Http\Requests\Laporan;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isPelapor();
    }

    public function rules(): array
    {
        return [
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'nip_nik' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],

            'gedung_id' => ['required', 'exists:gedung,id'],
            'lokasi_id' => ['required', 'exists:lokasi,id'],
            'jenis_kerusakan_id' => ['required', 'exists:jenis_kerusakan,id'],
            'fasilitas_id' => ['nullable', 'exists:fasilitas,id'],
            'nama_fasilitas_lainnya' => ['nullable', 'required_without:fasilitas_id', 'string', 'max:255'],
            'deskripsi_kerusakan' => ['required', 'string', 'max:2000'],

            'tingkat_urgensi_pelapor' => ['required', 'in:rendah,sedang,tinggi,darurat'],

            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'waktu_kejadian' => ['required', 'date_format:H:i'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'keterangan_tambahan' => ['nullable', 'string', 'max:2000'],

            'foto' => ['required', 'array', 'min:1', 'max:5'],
            'foto.*' => ['required', 'file', 'mimes:jpeg,jpg,png,webp,mp4,mov,webm', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'gedung_id.required' => 'Gedung wajib dipilih.',
            'lokasi_id.required' => 'Lokasi wajib dipilih.',
            'jenis_kerusakan_id.required' => 'Jenis kerusakan wajib dipilih.',
            'nama_fasilitas_lainnya.required_without' => 'Isi nama barang/fasilitas jika tidak ada di daftar pilihan.',
            'deskripsi_kerusakan.required' => 'Deskripsi kerusakan wajib diisi.',
            'tanggal_kejadian.before_or_equal' => 'Tanggal kejadian tidak boleh di masa depan.',
            'foto.required' => 'Unggah minimal 1 foto/video kerusakan.',
            'foto.max' => 'Maksimal 5 file per laporan.',
            'foto.*.mimes' => 'File harus berupa foto (JPG/PNG/WEBP) atau video (MP4/MOV/WEBM).',
            'foto.*.max' => 'Ukuran setiap file maksimal 20MB.',
        ];
    }
}
