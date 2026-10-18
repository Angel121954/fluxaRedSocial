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
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Debes seleccionar tu rol profesional',
            'role.in' => 'El rol profesional seleccionado no es válido',
        ];
    }
}
