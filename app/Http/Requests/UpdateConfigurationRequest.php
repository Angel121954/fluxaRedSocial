<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'bio' => ['nullable', 'string', 'min:5', 'max:160'],
            'location' => ['required', 'string', 'min:2'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre debe tener un máximo de 255 caracteres',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.max' => 'El nombre de usuario debe tener un máximo de 255 caracteres',
            'username.unique' => 'Este nombre de usuario ya está en uso',
            'bio.min' => 'La biografía debe tener un mínimo de 5 caracteres',
            'bio.max' => 'La biografía debe tener un máximo de 160 caracteres',
            'location.required' => 'La ubicación es obligatoria',
            'website_url.url' => 'El sitio web debe ser una URL válida',
            'github_url.url' => 'El enlace de GitHub debe ser una URL válida',
            'twitter_url.url' => 'El enlace de Twitter debe ser una URL válida',
            'linkedin_url.url' => 'El enlace de LinkedIn debe ser una URL válida',
            'birth_date.date' => 'La fecha de nacimiento no es válida',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'gender.in' => 'El género seleccionado no es válido',
        ];
    }
}
