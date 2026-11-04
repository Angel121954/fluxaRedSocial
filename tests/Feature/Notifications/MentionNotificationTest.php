<?php

declare(strict_types=1);

use App\Models\Diary;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function mentionUser(string $username): User
{
    return User::factory()->create([
        'username' => $username,
        'onboarding_completed' => true,
    ]);
}

describe('Menciones', function () {
    it('notifica una mención en un comentario de proyecto', function () {
        $author = mentionUser('autor_proyecto');
        $commenter = mentionUser('comentarista');
        $mentioned = mentionUser('mencionado');

        $project = Project::factory()->public()->for($author)->create();

        $this->actingAs($commenter)
            ->postJson(route('comments.store', $project), [
                'content' => 'Hola @mencionado, revisa esto.',
            ])
            ->assertOk();

        $notification = Notification::query()
            ->where('user_id', $mentioned->id)
            ->where('type', Notification::TYPE_MENTION)
            ->first();

        expect($notification)->not->toBeNull()
            ->and($notification->link)->toBe('/projects/'.$project->slug)
            ->and($notification->from_user_id)->toBe($commenter->id);

        expect(Notification::query()
            ->where('user_id', $commenter->id)
            ->where('type', Notification::TYPE_MENTION)
            ->doesntExist())->toBeTrue();
    });

    it('notifica una mención en una respuesta del Diario', function () {
        $diary = Diary::create([
            'question' => '¿Qué aprendiste hoy?',
            'status' => 'active',
        ]);
        $author = mentionUser('autor_diario');
        $mentioned = mentionUser('mencionado_diario');

        $this->actingAs($author)
            ->postJson(route('diary.reply'), [
                'content' => 'Hoy aprendí algo nuevo con @mencionado_diario.',
            ])
            ->assertOk();

        $notification = Notification::query()
            ->where('user_id', $mentioned->id)
            ->where('type', Notification::TYPE_MENTION)
            ->first();

        expect($notification)->not->toBeNull()
            ->and($notification->link)->toBe('/diary')
            ->and($notification->from_user_id)->toBe($author->id);
    });
});
