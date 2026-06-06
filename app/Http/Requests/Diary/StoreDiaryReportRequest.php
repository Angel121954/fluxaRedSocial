<?php

declare(strict_types=1);

namespace App\Http\Requests\Diary;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiaryReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Debes explicar el motivo del reporte.',
            'reason.min' => 'El motivo debe tener al menos 10 caracteres.',
        ];
    }
}
