<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'El mensaje no puede estar vacío',
            'body.string' => 'El mensaje debe ser texto',
            'body.max' => 'El mensaje no puede exceder 2000 caracteres',
        ];
    }
}
