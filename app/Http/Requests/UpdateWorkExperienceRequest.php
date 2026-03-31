<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'location' => 'nullable|string|max:100',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at|required_if:current,0',
            'current' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
