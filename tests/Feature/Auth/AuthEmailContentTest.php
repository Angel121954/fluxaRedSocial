<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

describe('Correos de autenticación', function () {
    it('genera el correo de verificación en español con la marca de Fluxa', function () {
        $user = User::factory()->unverified()->create();

        $mail = (new VerifyEmailNotification)->toMail($user);

        expect($mail->subject)->toBe('Verifica tu correo electrónico en Fluxa')
            ->and($mail->markdown)->toBe('emails.verify-email')
            ->and($mail->viewData['url'])->toContain('/email/verify/')
            ->and($mail->viewData['expire'])->toBe(60);

        $html = (string) app(Markdown::class)->render('emails.verify-email', $mail->viewData);

        expect($html)
            ->toContain('Hola, '.$user->name)
            ->toContain('Verificar mi correo')
            ->toContain('El equipo de Fluxa');
    });

    it('genera el correo de restablecimiento en español con la marca de Fluxa', function () {
        $user = User::factory()->create();

        $mail = (new ResetPasswordNotification('token'))->toMail($user);

        expect($mail->subject)->toBe('Restablece tu contraseña de Fluxa')
            ->and($mail->markdown)->toBe('emails.reset-password')
            ->and($mail->viewData['url'])->toContain('reset-password/token')
            ->and($mail->viewData['expire'])->toBe(60);

        $html = (string) app(Markdown::class)->render('emails.reset-password', $mail->viewData);

        expect($html)
            ->toContain('Hola, '.$user->name)
            ->toContain('Restablecer contraseña')
            ->toContain('El equipo de Fluxa');
    });

    it('usa las notificaciones personalizadas al enviar los correos', function () {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $user->sendEmailVerificationNotification();
        Notification::assertSentTo($user, VerifyEmailNotification::class);

        $user->sendPasswordResetNotification('token');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    });

    it('traduce el pie de los correos al español', function () {
        app()->setLocale('es');

        expect(__('All rights reserved.'))->toBe('Todos los derechos reservados.');
    });
});
