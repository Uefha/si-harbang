<?php

namespace App\Http\Requests\Laporan;

use Illuminate\Foundation\Http\FormRequest;

class UploadFotoLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('super_admin', 'harbang');
    }

    public function rules(): array
    {
        return [
            'tipe' => ['required', 'in:proses,selesai'],
            'foto' => ['required', 'array', 'min:1', 'max:5'],
            'foto.*' => ['required', 'file', 'mimes:jpeg,jpg,png,webp,mp4,mov,webm', 'max:20480'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'Pilih minimal 1 foto/video untuk diunggah.',
            'foto.*.mimes' => 'File harus berupa foto (JPG/PNG/WEBP) atau video (MP4/MOV/WEBM).',
            'foto.*.max' => 'Ukuran setiap file maksimal 20MB.',
        ];
    }
}
