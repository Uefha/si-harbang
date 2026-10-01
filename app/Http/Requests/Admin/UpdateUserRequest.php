<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            // Kosongkan berarti tidak diubah - dipakai Super Admin untuk
            // mengatur ulang kata sandi siapa pun, termasuk akunnya sendiri
            // (menggantikan reset password lewat email yang tidak dipakai lagi).
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
