<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LokasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('lokasi')?->id;

        return [
            'nama_lokasi' => ['required', 'string', 'max:255', Rule::unique('lokasi', 'nama_lokasi')->ignore($id)],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique' => 'Nama lokasi ini sudah terdaftar.',
        ];
    }
}
