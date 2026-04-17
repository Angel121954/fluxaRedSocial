<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'email_enabled' => ['nullable', 'in:on,1,0,true,false'],
            'push_enabled' => ['nullable', 'in:on,1,0,true,false'],
            'notify_comments' => ['nullable', 'in:on,1,0,true,false'],
            'notify_followers' => ['nullable', 'in:on,1,0,true,false'],
            'notify_mentions' => ['nullable', 'in:on,1,0,true,false'],
            'weekly_summary' => ['nullable', 'in:on,1,0,true,false'],
        ];
    }

    public function messages(): array
    {
        return [
            'email_enabled.in' => 'El campo notificaciones por correo debe ser una opción válida.',
            'push_enabled.in' => 'El campo notificaciones push debe ser una opción válida.',
            'notify_comments.in' => 'El campo notificaciones de comentarios debe ser una opción válida.',
            'notify_followers.in' => 'El campo notificaciones de seguidores debe ser una opción válida.',
            'notify_mentions.in' => 'El campo notificaciones de menciones debe ser una opción válida.',
            'weekly_summary.in' => 'El campo resumen semanal debe ser una opción válida.',
        ];
    }
}
