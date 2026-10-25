<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Restablece tu contraseña de Fluxa')
            ->markdown('emails.reset-password', [
                'url' => $this->resetUrl($notifiable),
                'user' => $notifiable,
                'expire' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]);
    }
}
