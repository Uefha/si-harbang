<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StorePelaporRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'nip_nik' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
        ];
    }
}
