<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use App\Models\Credential;
use Illuminate\Foundation\Http\FormRequest;

class StoreCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'issuer' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:'.implode(',', array_keys(Credential::TYPES))],
            'issued_at' => ['required', 'date', 'before_or_equal:today'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'credential_id' => ['nullable', 'string', 'max:100'],
            'credential_url' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'description' => ['nullable', 'string', 'max:500'],
            'privacy' => ['required', 'in:'.implode(',', array_keys(Credential::PRIVACY))],
        ];
    }
}
