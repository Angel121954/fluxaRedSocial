<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GrantBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
            'badge_slug' => 'required|string|exists:badges,slug',
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required' => 'Debes seleccionar al menos un usuario',
            'user_ids.array' => 'Los usuarios deben enviarse como una lista',
            'user_ids.min' => 'Debes seleccionar al menos un usuario',
            'user_ids.*.required' => 'Cada usuario debe ser un ID válido',
            'user_ids.*.exists' => 'Uno o más usuarios no existen',
            'badge_slug.required' => 'La insignia es requerida',
            'badge_slug.string' => 'La insignia debe ser texto',
            'badge_slug.exists' => 'La insignia no existe',
        ];
    }
}
