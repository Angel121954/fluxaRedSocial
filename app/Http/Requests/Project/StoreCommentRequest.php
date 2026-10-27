<?php

declare(strict_types=1);

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return [
            'content' => 'required|string|max:1000',
            'parent_id' => [
                'nullable',
                Rule::exists('comments', 'id')
                    ->where(fn ($query) => $query->where('project_id', $projectId)),
            ],
        ];
    }
}
