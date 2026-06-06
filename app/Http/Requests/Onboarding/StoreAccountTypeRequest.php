<?php

declare(strict_types=1);

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'account_type' => 'required|in:developer,company',
        ];
    }

    public function messages(): array
    {
        return [
            'account_type.required' => 'Debes seleccionar un tipo de cuenta',
            'account_type.in' => 'El tipo de cuenta seleccionado no es válido',
        ];
    }
}
