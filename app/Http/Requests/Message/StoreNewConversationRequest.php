<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'body' => 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es requerido',
            'user_id.integer' => 'El ID del usuario debe ser un número',
            'user_id.exists' => 'El usuario no existe',
            'body.required' => 'El mensaje no puede estar vacío',
            'body.string' => 'El mensaje debe ser texto',
            'body.max' => 'El mensaje no puede exceder 2000 caracteres',
        ];
    }
}
