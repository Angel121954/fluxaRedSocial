<?php

declare(strict_types=1);

namespace App\Http\Requests\Diary;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiaryResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:500',
        ];
    }
}
