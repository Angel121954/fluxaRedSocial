<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrivacyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'visibility' => ['nullable', 'string', 'in:on,1,private'],
            'accept_messages' => ['nullable', 'in:on,1,0,true,false'],
            'show_email' => ['nullable', 'in:on,1,0,true,false'],
        ];
    }
}
