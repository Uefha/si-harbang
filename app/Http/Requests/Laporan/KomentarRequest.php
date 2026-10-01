<?php

namespace App\Http\Requests\Laporan;

use Illuminate\Foundation\Http\FormRequest;

class KomentarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pesan' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pesan.required' => 'Tulis pesan terlebih dahulu.',
        ];
    }
}
