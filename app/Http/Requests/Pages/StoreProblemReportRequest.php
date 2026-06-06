<?php

declare(strict_types=1);

namespace App\Http\Requests\Pages;

use Illuminate\Foundation\Http\FormRequest;

class StoreProblemReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:error_tecnico,contenido_inapropiado,problema_cuenta,otro'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Selecciona el tipo de problema.',
            'type.in' => 'El tipo de problema seleccionado no es válido.',
            'message.required' => 'Describe el problema.',
            'message.min' => 'La descripción debe tener al menos 10 caracteres.',
            'message.max' => 'La descripción no puede exceder 2000 caracteres.',
        ];
    }
}
