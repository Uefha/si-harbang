<?php

namespace App\Http\Requests\Laporan;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiLaporanRequest extends FormRequest
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
            'prioritas_id' => ['required', 'exists:prioritas,id'],
            'petugas_harbang_id' => ['nullable', 'exists:petugas_harbang,id'],
            'catatan_harbang' => ['nullable', 'string', 'max:2000'],
            'estimasi_selesai' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'prioritas_id.required' => 'Tentukan tingkat prioritas laporan ini.',
        ];
    }
}
