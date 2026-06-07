<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaMessageRequest extends FormRequest
{
    private const int MAX_FILE_SIZE = 25 * 1024; // 25 MB en kilobytes
    private const int MAX_IMAGE_SIZE = 15 * 1024; // 15 MB en kilobytes

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $mediaType = $this->input('media_type', 'file');

        $rules = [
            'media_type' => 'required|string|in:image,file',
            'body' => 'nullable|string|max:2000',
        ];

        if ($mediaType === 'image') {
            $rules['file'] = [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:' . self::MAX_IMAGE_SIZE,
            ];
        } else {
            $rules['file'] = [
                'required',
                'file',
                'max:' . self::MAX_FILE_SIZE,
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Debes seleccionar un archivo.',
            'file.mimes' => 'La imagen debe ser JPG, PNG, GIF o WebP.',
            'file.max' => 'El archivo no puede superar los :max kilobytes.',
            'media_type.required' => 'El tipo de medio es requerido.',
            'media_type.in' => 'El tipo de medio no es válido.',
            'body.max' => 'El mensaje no puede exceder 2000 caracteres.',
        ];
    }
}
