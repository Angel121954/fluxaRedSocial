<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:100',
            'content' => 'required|string|min:10|max:500',
            'privacy' => 'nullable|in:public,followers,private',
            'techs' => 'nullable|array',
            'techs.*' => 'string|max:50',
            'media' => 'nullable|array|max:6',
            'media.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,webm|max:10240',
        ];
    }
}
