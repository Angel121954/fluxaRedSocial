<?php

declare(strict_types=1);

namespace App\Http\Requests\Notification;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StorePushSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'endpoint' => [
                'required',
                'string',
                'url',
                'max:500',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $this->isAllowedEndpoint((string) $value)) {
                        $fail('El endpoint de notificaciones no es válido.');
                    }
                },
            ],
            'public_key' => ['nullable', 'string', 'max:255'],
            'auth_token' => ['nullable', 'string', 'max:255'],
            'content_encoding' => ['nullable', 'string', 'in:aesgcm,aes128gcm'],
        ];
    }

    private function isAllowedEndpoint(string $endpoint): bool
    {
        $parts = parse_url($endpoint);

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

        if ($host === '') {
            return false;
        }

        $exactHosts = [
            'fcm.googleapis.com',
            'android.googleapis.com',
            'updates.push.services.mozilla.com',
            'push.services.mozilla.com',
            'web.push.apple.com',
        ];

        if (in_array($host, $exactHosts, true)) {
            return true;
        }

        $allowedSuffixes = [
            '.notify.windows.com',
            '.push.apple.com',
            '.push.services.mozilla.com',
        ];

        foreach ($allowedSuffixes as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        return false;
    }
}
