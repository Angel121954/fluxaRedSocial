<?php

declare(strict_types=1);

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class BookmarkJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'job_id' => 'required|exists:jobs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => 'El ID de la oferta es requerido',
            'job_id.exists' => 'La oferta no existe',
        ];
    }
}
