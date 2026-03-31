<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution' => ['required', 'string', 'max:150'],
            'degree' => ['required', 'string', 'max:150'],
            'field' => ['nullable', 'string', 'max:150'],
            'graduated_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 10)],
            'current' => ['nullable', 'boolean'],
        ];
    }
}
