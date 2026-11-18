<?php

declare(strict_types=1);

use App\Mail\WeeklySummary;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

describe('Correos de notificaciones', function () {
    it('no envía un correo por cada interacción', function () {
        Mail::fake();

        $user = User::factory()->create(['onboarding_completed' => true]);
        $sender = User::factory()->create(['onboarding_completed' => true]);

        NotificationPreference::create([
            'user_id' => $user->id,
            'email_enabled' => true,
            'notify_followers' => true,
            'notify_comments' => true,
        ]);

        NotificationService::createNotification(
            userId: $user->id,
            type: Notification::TYPE_FOLLOW,
            title: 'Nuevo seguidor',
            body: 'comenzó a seguirte',
            fromUserId: $sender->id,
        );

        NotificationService::createNotification(
            userId: $user->id,
            type: Notification::TYPE_COMMENT,
            title: 'Nuevo comentario',
            body: 'comentó en tu proyecto',
            fromUserId: $sender->id,
        );

        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    });

    it('envía el resumen semanal con un enlace de baja firmado', function () {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        NotificationPreference::create([
            'user_id' => $user->id,
            'email_enabled' => true,
            'weekly_summary' => true,
        ]);

        $this->artisan('notifications:weekly-summary', ['--user' => $user->id])
            ->assertSuccessful();

        Mail::assertQueued(WeeklySummary::class, function (WeeklySummary $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && str_contains($mail->unsubscribeUrl, 'signature=')
                && str_contains($mail->unsubscribeOneClickUrl, 'signature=');
        });
    });

    it('no envía el resumen semanal a quien está dado de baja', function () {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        NotificationPreference::create([
            'user_id' => $user->id,
            'email_enabled' => false,
            'weekly_summary' => true,
        ]);

        $this->artisan('notifications:weekly-summary', ['--user' => $user->id])
            ->assertSuccessful();

        Mail::assertNothingQueued();
    });

    it('da de baja al usuario desde un enlace firmado', function () {
        $user = User::factory()->create();

        NotificationPreference::create([
            'user_id' => $user->id,
            'email_enabled' => true,
            'weekly_summary' => true,
        ]);

        $showUrl = URL::temporarySignedRoute(
            'email.unsubscribe.show',
            now()->addDay(),
            ['user' => $user->id],
        );

        $this->get($showUrl)
            ->assertOk()
            ->assertSee('dejar de recibir el resumen semanal');

        $destroyUrl = URL::signedRoute(
            'email.unsubscribe.destroy',
            ['user' => $user->id],
        );

        $this->post($destroyUrl)
            ->assertRedirect(route('landing'));

        $preferences = $user->fresh()->notificationPreferences;

        expect($preferences->email_enabled)->toBeFalse()
            ->and($preferences->weekly_summary)->toBeFalse();
    });

    it('permite la baja en un clic desde el cliente de correo', function () {
        $user = User::factory()->create();

        NotificationPreference::create([
            'user_id' => $user->id,
            'email_enabled' => true,
            'weekly_summary' => true,
        ]);

        $url = URL::temporarySignedRoute(
            'email.unsubscribe.destroy',
            now()->addDay(),
            ['user' => $user->id],
        );

        $this->post($url, [], ['List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click'])
            ->assertNoContent();

        $preferences = $user->fresh()->notificationPreferences;

        expect($preferences->email_enabled)->toBeFalse()
            ->and($preferences->weekly_summary)->toBeFalse();
    });

    it('rechaza enlaces de baja sin firma válida', function () {
        $user = User::factory()->create();

        $this->get(route('email.unsubscribe.show', $user))
            ->assertForbidden();
    });
});
