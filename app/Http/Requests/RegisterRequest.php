<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/',
                function ($attribute, $value, $fail) {
                    $reserved = [
                        'avatar', 'create', 'edit', 'delete', 'update', 'store', 'show',
                        'profile', 'dashboard', 'explore', 'search', 'trending', 'recent',
                        'following', 'topic', 'technologies', 'about-fluxa', 'about',
                        'configuration', 'account', 'security', 'privacy',
                        'notifications', 'notification-preference',
                        'projects', 'work-experiences', 'educations', 'suggestions',
                        'admin', 'onboarding', 'cv', 'download',
                        'guest', 'auth', 'login', 'register', 'logout',
                        'password', 'email', 'verify', 'confirm',
                        'home', 'index', 'api', 'user', 'users',
                    ];
                    if (in_array(strtolower($value), $reserved)) {
                        $fail('Este nombre de usuario no está disponible.');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(6),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede superar 100 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.max' => 'El nombre de usuario no puede superar 30 caracteres.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números y guiones bajos.',
            'username.not_regex' => 'Este nombre de usuario no está permitido.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no tiene un formato válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
