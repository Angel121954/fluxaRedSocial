<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaMessageRequest extends FormRequest
{
    private const int MAX_FILE_SIZE = 25 * 1024; // 25 MB en kilobytes

    private const int MAX_IMAGE_SIZE = 15 * 1024; // 15 MB en kilobytes

    private const string ALLOWED_FILE_EXTENSIONS = 'pdf,doc,docx,xls,xlsx,ppt,pptx,odt,ods,odp,txt,csv,zip';

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $mediaType = $this->input('media_type', 'file');

        $rules = [
            'media_type' => 'required|string|in:image,file,gif',
            'body' => 'nullable|string|max:2000',
        ];

        if ($mediaType === 'gif') {
            $rules['gif_url'] = [
                'required',
                'url',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $this->isAllowedGifUrl((string) $value)) {
                        $fail('La URL del GIF no es válida.');
                    }
                },
            ];
        } elseif ($mediaType === 'image') {
            $rules['file'] = [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:'.self::MAX_IMAGE_SIZE,
            ];
        } else {
            $rules['file'] = [
                'required',
                'file',
                'mimes:'.self::ALLOWED_FILE_EXTENSIONS,
                'max:'.self::MAX_FILE_SIZE,
            ];
        }

        return $rules;
    }

    private function isAllowedGifUrl(string $url): bool
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return false;
        }

        if (strtolower($parts['scheme']) !== 'https') {
            return false;
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }

        $port = $parts['port'] ?? null;

        if ($port !== null && $port !== 443) {
            return false;
        }

        $host = strtolower(rtrim($parts['host'], '.'));

        return $host === 'giphy.com' || str_ends_with($host, '.giphy.com');
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Debes seleccionar un archivo.',
            'file.mimes' => 'El archivo adjunto no tiene un formato permitido.',
            'file.max' => 'El archivo no puede superar los :max kilobytes.',
            'gif_url.required' => 'La URL del GIF es requerida.',
            'gif_url.url' => 'La URL del GIF no es válida.',
            'media_type.required' => 'El tipo de medio es requerido.',
            'media_type.in' => 'El tipo de medio no es válido.',
            'body.max' => 'El mensaje no puede exceder 2000 caracteres.',
        ];
    }
}
