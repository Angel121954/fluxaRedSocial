<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'phone_code' => ['required', 'string', 'max:5'],
            'phone_number' => ['required', 'string', 'max:15'],
            'language' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Este email ya está registrado.',
            'phone_code.required' => 'El código de marcación internacional es obligatorio',
        ];
    }
}
