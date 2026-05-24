<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiaryComment extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:diary_response_comments,id',
        ];
    }
}
