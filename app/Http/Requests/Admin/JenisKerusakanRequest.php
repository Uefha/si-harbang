<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JenisKerusakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('jenisKerusakan')?->id;

        return [
            'nama_jenis' => ['required', 'string', 'max:255', Rule::unique('jenis_kerusakan', 'nama_jenis')->ignore($id)],
            'icon' => ['nullable', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_jenis.required' => 'Nama jenis kerusakan wajib diisi.',
            'nama_jenis.unique' => 'Jenis kerusakan ini sudah terdaftar.',
        ];
    }
}
