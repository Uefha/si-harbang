<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePetugasHarbangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('petugasHarbang')?->user_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'nip' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'spesialisasi' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
