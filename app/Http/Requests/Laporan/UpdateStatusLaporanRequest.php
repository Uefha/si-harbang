<?php

namespace App\Http\Requests\Laporan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(
            \App\Models\Role::SUPER_ADMIN,
            \App\Models\Role::HARBANG
        );
    }

    public function rules(): array
    {
        return [
            'status_id' => ['required', 'exists:status_laporan,id'],
            'petugas_harbang_id' => ['nullable', 'exists:petugas_harbang,id'],
            'catatan_harbang' => ['nullable', 'string', 'max:2000'],
            'estimasi_selesai' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'status_id.required' => 'Pilih status baru untuk laporan ini.',
        ];
    }
}
