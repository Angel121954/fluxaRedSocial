<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'location' => 'nullable|string|max:100',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at|required_if:current,0',
            'current' => 'nullable|in:on,1,0,true,false',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
