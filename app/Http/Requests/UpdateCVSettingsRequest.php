<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCVSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'template' => 'required|string|in:classic,modern,creative',
            'show_photo' => 'nullable|in:on,1,0,true,false',
            'show_location' => 'nullable|in:on,1,0,true,false',
            'show_email' => 'nullable|in:on,1,0,true,false',
            'show_projects' => 'nullable|in:on,1,0,true,false',
            'show_experience' => 'nullable|in:on,1,0,true,false',
            'show_education' => 'nullable|in:on,1,0,true,false',
        ];
    }
}
