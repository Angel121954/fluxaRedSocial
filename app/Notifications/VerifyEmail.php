<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifica tu correo electrónico en Fluxa')
            ->markdown('emails.verify-email', [
                'url' => $this->verificationUrl($notifiable),
                'user' => $notifiable,
                'expire' => config('auth.verification.expire', 60),
            ]);
    }
}
