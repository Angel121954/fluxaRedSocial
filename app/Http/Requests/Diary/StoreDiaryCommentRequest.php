<?php

declare(strict_types=1);

namespace App\Http\Requests\Diary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiaryCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $responseId = $this->route('response')?->id;

        return [
            'content' => 'required|string|max:500',
            'parent_id' => [
                'nullable',
                Rule::exists('diary_response_comments', 'id')
                    ->where(fn ($query) => $query->where('diary_response_id', $responseId)),
            ],
        ];
    }
}
