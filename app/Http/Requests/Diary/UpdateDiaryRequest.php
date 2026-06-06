<?php

declare(strict_types=1);

namespace App\Http\Requests\Diary;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'question' => 'required|string|max:500',
            'emoji' => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'La pregunta es requerida',
            'question.string' => 'La pregunta debe ser texto',
            'question.max' => 'La pregunta no puede exceder 500 caracteres',
            'emoji.string' => 'El emoji debe ser texto',
            'emoji.max' => 'El emoji no puede exceder 10 caracteres',
        ];
    }
}
