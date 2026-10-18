<?php

declare(strict_types=1);

use App\Jobs\SendGroupedLikePush;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Notifications\CreatesNotifications;
use App\Notifications\WebPushNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification as NotificationFacade;

uses(RefreshDatabase::class);

describe('Suscripción a notificaciones push', function () {
    it('guarda la suscripción del usuario autenticado', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/notifications/push/subscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-1',
            'public_key' => 'dummy-public-key',
            'auth_token' => 'dummy-auth-token',
            'content_encoding' => 'aesgcm',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_type' => $user->getMorphClass(),
            'subscribable_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-1',
        ]);
    });

    it('no permite suscribirse sin autenticación', function () {
        $response = $this->postJson('/notifications/push/subscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-2',
        ]);

        $response->assertUnauthorized();
    });

    it('elimina la suscripción del usuario por endpoint', function () {
        $user = User::factory()->create();
        $user->updatePushSubscription(
            endpoint: 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-3',
            key: 'key',
            token: 'token',
            contentEncoding: 'aesgcm'
        );

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-3',
        ]);

        $this->actingAs($user)->deleteJson('/notifications/push/subscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-3',
        ])->assertOk();

        $this->assertDatabaseMissing('push_subscriptions', [
            'subscribable_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-3',
        ]);
    });
});

describe('Envío de notificaciones push', function () {
    it('no envía push cuando el usuario tiene push deshabilitado', function () {
        NotificationFacade::fake();

        $user = User::factory()->create();

        NotificationPreference::create([
            'user_id' => $user->id,
            'push_enabled' => false,
        ]);

        CreatesNotifications::createNotification(
            userId: $user->id,
            type: Notification::TYPE_FOLLOW,
            title: 'Nuevo seguidor',
            body: 'alguien comenzó a seguirte',
        );

        NotificationFacade::assertNothingSentTo($user);
    });

    it('no envía push sin suscripciones activas', function () {
        NotificationFacade::fake();

        $user = User::factory()->create();

        NotificationPreference::create([
            'user_id' => $user->id,
            'push_enabled' => true,
        ]);

        CreatesNotifications::createNotification(
            userId: $user->id,
            type: Notification::TYPE_COMMENT,
            title: 'Nuevo comentario',
            body: 'alguien comentó tu proyecto',
        );

        NotificationFacade::assertNothingSentTo($user);
    });

    it('envía push cuando hay suscripción y push habilitado', function () {
        NotificationFacade::fake();

        $user = User::factory()->create();
        $user->updatePushSubscription(
            endpoint: 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-4',
            key: 'key',
            token: 'token',
            contentEncoding: 'aesgcm'
        );

        NotificationPreference::create([
            'user_id' => $user->id,
            'push_enabled' => true,
            'notify_followers' => true,
        ]);

        CreatesNotifications::createNotification(
            userId: $user->id,
            type: Notification::TYPE_FOLLOW,
            title: 'Nuevo seguidor',
            body: 'alguien comenzó a seguirte',
            link: '/profile/otro-usuario',
            fromUserId: User::factory()->create()->id,
        );

        NotificationFacade::assertSentTo($user, WebPushNotification::class);
    });

    it('respeta la preferencia del tipo de notificación al enviar push', function () {
        NotificationFacade::fake();

        $user = User::factory()->create();
        $user->updatePushSubscription(
            endpoint: 'https://fcm.googleapis.com/fcm/send/dummy-endpoint-5',
            key: 'key',
            token: 'token',
            contentEncoding: 'aesgcm'
        );

        NotificationPreference::create([
            'user_id' => $user->id,
            'push_enabled' => true,
            'notify_followers' => false,
        ]);

        CreatesNotifications::createNotification(
            userId: $user->id,
            type: Notification::TYPE_FOLLOW,
            title: 'Nuevo seguidor',
            body: 'alguien comenzó a seguirte',
            fromUserId: User::factory()->create()->id,
        );

        NotificationFacade::assertNothingSentTo($user);
    });
});

describe('Agrupación de likes (push)', function () {
    it('acumula likes de personas distintas en un solo grupo', function () {
        $user = User::factory()->create();
        $user->updatePushSubscription(
            endpoint: 'https://fcm.googleapis.com/fcm/send/dummy-like-1',
            key: 'key',
            token: 'token',
            contentEncoding: 'aesgcm'
        );
        NotificationPreference::create([
            'user_id' => $user->id,
            'push_enabled' => true,
            'notify_comments' => true,
        ]);

        CreatesNotifications::queueLikePush(
            userId: $user->id,
            likerName: 'sara',
            referenceId: 1,
            referenceType: 'project',
            link: '/projects/1'
        );
        CreatesNotifications::queueLikePush(
            userId: $user->id,
            likerName: 'juan',
            referenceId: 1,
            referenceType: 'project',
            link: '/projects/1'
        );
        CreatesNotifications::queueLikePush(
            userId: $user->id,
            likerName: 'sara',
            referenceId: 1,
            referenceType: 'project',
            link: '/projects/1'
        );

        $pending = Cache::store('redis')->get("push.pending_likes.{$user->id}.project.1");

        expect($pending['names'])->toBe(['sara', 'juan'])
            ->and($pending['count'])->toBe(2);
    });

    it('envía un solo push agrupado con personas únicas en el job', function () {
        NotificationFacade::fake();

        $user = User::factory()->create();
        $user->updatePushSubscription(
            endpoint: 'https://fcm.googleapis.com/fcm/send/dummy-like-2',
            key: 'key',
            token: 'token',
            contentEncoding: 'aesgcm'
        );
        NotificationPreference::create([
            'user_id' => $user->id,
            'push_enabled' => true,
            'notify_comments' => true,
        ]);

        CreatesNotifications::queueLikePush(
            userId: $user->id,
            likerName: 'sara',
            referenceId: 1,
            referenceType: 'project',
            link: '/projects/1'
        );
        CreatesNotifications::queueLikePush(
            userId: $user->id,
            likerName: 'juan',
            referenceId: 1,
            referenceType: 'project',
            link: '/projects/1'
        );

        (new SendGroupedLikePush($user->id, 1, 'project', '/projects/1'))->handle();

        NotificationFacade::assertSentTo($user, WebPushNotification::class, function ($notification) {
            return str_contains($notification->body, 'y a 1 más les gustó');
        });

        expect(Cache::store('redis')->has("push.pending_likes.{$user->id}.project.1"))->toBeFalse()
            ->and(Cache::store('redis')->has("push.pending_likes.lock.{$user->id}.project.1"))->toBeFalse();
    });
});

describe('Límite de rate por usuario', function () {
    it('no envía un segundo push del mismo tipo dentro de la ventana de rate', function () {
        NotificationFacade::fake();

        $user = User::factory()->create();
        $user->updatePushSubscription(
            endpoint: 'https://fcm.googleapis.com/fcm/send/dummy-rate-1',
            key: 'key',
            token: 'token',
            contentEncoding: 'aesgcm'
        );
        NotificationPreference::create([
            'user_id' => $user->id,
            'push_enabled' => true,
        ]);

        for ($i = 0; $i < 2; $i++) {
            CreatesNotifications::createNotification(
                userId: $user->id,
                type: Notification::TYPE_PROJECT,
                title: 'Nuevo proyecto',
                body: 'Tienes un nuevo proyecto',
                fromUserId: User::factory()->create()->id,
            );
        }

        NotificationFacade::assertSentToTimes($user, WebPushNotification::class, 1);
    });
});
