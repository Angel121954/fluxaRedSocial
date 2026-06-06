<?php

declare(strict_types=1);

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreBioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'bio' => 'nullable|string|max:400',
        ];
    }

    public function messages(): array
    {
        return [
            'bio.string' => 'La biografía debe ser texto',
            'bio.max' => 'La biografía no puede exceder 400 caracteres',
        ];
    }
}
