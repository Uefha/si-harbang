<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GedungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('gedung')?->id;

        return [
            'kode_gedung' => ['nullable', 'string', 'max:20', Rule::unique('gedung', 'kode_gedung')->ignore($id)],
            'nama_gedung' => ['required', 'string', 'max:255', Rule::unique('gedung', 'nama_gedung')->ignore($id)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_gedung.required' => 'Nama gedung wajib diisi.',
            'nama_gedung.unique' => 'Nama gedung ini sudah terdaftar.',
            'kode_gedung.unique' => 'Kode gedung ini sudah dipakai.',
        ];
    }
}
