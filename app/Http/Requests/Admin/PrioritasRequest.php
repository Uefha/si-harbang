<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrioritasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('prioritas')?->id;

        return [
            'nama' => ['required', 'string', 'max:30', Rule::unique('prioritas', 'nama')->ignore($id)],
            'sla_hari' => ['required', 'integer', 'min:1', 'max:365'],
            'warna_badge' => ['required', Rule::in(['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'])],
            'urutan' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama prioritas wajib diisi.',
            'sla_hari.required' => 'Batas SLA (hari) wajib diisi.',
            'sla_hari.min' => 'Batas SLA minimal 1 hari.',
        ];
    }
}
