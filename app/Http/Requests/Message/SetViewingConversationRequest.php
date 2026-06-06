<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class SetViewingConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'conversation_id' => 'required|integer|exists:conversations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'conversation_id.required' => 'El ID de la conversación es requerido',
            'conversation_id.integer' => 'El ID de la conversación debe ser un número',
            'conversation_id.exists' => 'La conversación no existe',
        ];
    }
}
