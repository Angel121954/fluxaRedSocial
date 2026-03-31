<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:100',
            'content' => 'required|string|min:10|max:500',
            'privacy' => 'nullable|in:public,followers,private',
            'techs' => 'nullable|array',
            'techs.*' => 'string|max:50',
            'media' => 'nullable|array|max:6',
            'media.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,webm|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El titulo es requerido',
            'title.string' => 'El titulo debe ser cadena de texto',
            'title.min' => 'El titulo debe de tener un minimo de 3 caracteres',
            'title.max' => 'El titulo debe de tener un maximo de 100 caracteres',

            'content.required' => 'La descripcion del proyecto es requerida',
            'content.string' => 'La descripcion debe de ser cadena de texto',
            'content.min' => 'La descripcion debe de tener un minimo de 10 caracteres',
            'content.max' => 'La descripcion debe de tener un maximo de 500 caracteres',

            'privacy.in' => 'El tipo de privacidad seleccionado no es valido',

            'techs.array' => 'Las tecnologias deben enviarse como una lista',
            'techs.*.string' => 'Cada tecnologia debe ser texto',
            'techs.*.max' => 'Cada tecnologia puede tener maximo 50 caracteres',

            'media.array' => 'Los archivos deben enviarse como una lista',
            'media.max' => 'Solo puedes subir maximo 6 archivos',

            'media.*.file' => 'Cada archivo debe ser un archivo valido',
            'media.*.mimes' => 'Los archivos deben ser jpg, jpeg, png, webp, gif, mp4 o webm',
            'media.*.max' => 'Cada archivo puede pesar maximo 10MB',
        ];
    }
}
