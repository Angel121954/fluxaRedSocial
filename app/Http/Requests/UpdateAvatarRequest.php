<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'La imagen es obligatoria para poder actualizar',
            'avatar.image' => 'Debe ser una imagen válida',
            'avatar.max' => 'La imagen no puede superar los 2 MB',
        ];
    }
}
