<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FasilitasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('fasilitas')?->id;

        return [
            'jenis_kerusakan_id' => ['nullable', 'exists:jenis_kerusakan,id'],
            'nama_fasilitas' => ['required', 'string', 'max:255', Rule::unique('fasilitas', 'nama_fasilitas')->ignore($id)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_fasilitas.required' => 'Nama fasilitas wajib diisi.',
            'nama_fasilitas.unique' => 'Fasilitas ini sudah terdaftar.',
        ];
    }
}
