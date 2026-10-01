<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('status')?->id;

        return [
            'nama' => ['required', 'string', 'max:40', Rule::unique('status_laporan', 'nama')->ignore($id)],
            'warna_badge' => ['required', Rule::in(['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'])],
            'urutan' => ['required', 'integer', 'min:0'],
            'is_final' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama status wajib diisi.',
        ];
    }
}
