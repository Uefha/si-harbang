<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePelaporRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('pelapor')?->user_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')],
            // Password opsional saat ubah data - kosongkan jika tidak ingin diganti
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'nip_nik' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
