<?php

declare(strict_types=1);

namespace App\Http\Requests\Onboarding;

use App\Models\Profile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(Profile::SPECIALTIES)],
            'intent' => ['required', Rule::in(array_keys(Profile::INTENTS))],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Debes seleccionar tu rol profesional',
            'role.in' => 'El rol profesional seleccionado no es válido',
            'intent.required' => 'Debes seleccionar qué estás buscando',
            'intent.in' => 'La opción seleccionada no es válida',
        ];
    }
}
