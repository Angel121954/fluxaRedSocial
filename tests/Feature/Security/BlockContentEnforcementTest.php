<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Diary;
use App\Models\DiaryResponse;
use App\Models\DiaryResponseComment;
use App\Models\Notification;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function blockContentUser(string $visibility = 'public'): User
{
    $user = User::factory()->create(['onboarding_completed' => true]);

    Profile::factory()->for($user)->create([
        'visibility' => $visibility,
        'avatar' => null,
    ]);

    return $user->load('profile');
}

function blockContentBetween(User $blocker, User $blocked): void
{
    UserBlock::create([
        'blocker_id' => $blocker->id,
        'blocked_id' => $blocked->id,
    ]);
}

function blockContentProject(User $owner): Project
{
    return Project::factory()->public()->create([
        'user_id' => $owner->id,
        'title' => 'Proyecto compartido',
    ]);
}

describe('Bloqueos en contenido social', function () {
    it('oculta las respuestas del diario de usuarios bloqueados', function () {
        $viewer = blockContentUser();
        $blocked = blockContentUser();

        $diary = Diary::create([
            'question' => '¿Qué aprendiste hoy?',
            'emoji' => '🔥',
            'status' => 'active',
        ]);

        DiaryResponse::create([
            'diary_id' => $diary->id,
            'user_id' => $blocked->id,
            'content' => 'Respuesta de un usuario bloqueado',
        ]);

        blockContentBetween($viewer, $blocked);

        $this->actingAs($viewer)
            ->get(route('diary.index'))
            ->assertOk()
            ->assertDontSee('Respuesta de un usuario bloqueado');
    });

    it('impide interactuar con la respuesta del diario de un usuario bloqueado', function () {
        $viewer = blockContentUser();
        $blocked = blockContentUser();

        $diary = Diary::create([
            'question' => '¿Qué aprendiste hoy?',
            'emoji' => '🔥',
            'status' => 'active',
        ]);

        $response = DiaryResponse::create([
            'diary_id' => $diary->id,
            'user_id' => $blocked->id,
            'content' => 'Respuesta de un usuario bloqueado',
        ]);

        blockContentBetween($viewer, $blocked);

        $this->actingAs($viewer)
            ->postJson(route('diary.response.like', $response))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->postJson(route('diary.response.bookmark', $response))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->postJson(route('diary.response.comment', $response), [
                'content' => 'Comentario',
            ])
            ->assertForbidden();
    });

    it('oculta comentarios de usuarios bloqueados en el diario', function () {
        $viewer = blockContentUser();
        $blocked = blockContentUser();
        $owner = blockContentUser();

        $diary = Diary::create([
            'question' => '¿Qué aprendiste hoy?',
            'emoji' => '🔥',
            'status' => 'active',
        ]);

        $response = DiaryResponse::create([
            'diary_id' => $diary->id,
            'user_id' => $owner->id,
            'content' => 'Respuesta del diario',
        ]);

        DiaryResponseComment::create([
            'diary_response_id' => $response->id,
            'user_id' => $blocked->id,
            'content' => 'Comentario de un usuario bloqueado',
        ]);

        blockContentBetween($viewer, $blocked);

        $this->actingAs($viewer)
            ->getJson(route('diary.response.comments', $response))
            ->assertOk()
            ->assertJsonMissing(['content' => 'Comentario de un usuario bloqueado']);
    });

    it('oculta comentarios de usuarios bloqueados en proyectos de terceros', function () {
        $viewer = blockContentUser();
        $blocked = blockContentUser();
        $owner = blockContentUser();
        $project = blockContentProject($owner);

        Comment::create([
            'user_id' => $blocked->id,
            'project_id' => $project->id,
            'content' => 'Comentario de un usuario bloqueado',
        ]);

        blockContentBetween($viewer, $blocked);

        $this->actingAs($viewer)
            ->getJson(route('comments.index', $project))
            ->assertOk()
            ->assertJsonMissing(['content' => 'Comentario de un usuario bloqueado']);
    });

    it('impide dar like al comentario de un usuario bloqueado', function () {
        $viewer = blockContentUser();
        $blocked = blockContentUser();
        $owner = blockContentUser();
        $project = blockContentProject($owner);

        $comment = Comment::create([
            'user_id' => $blocked->id,
            'project_id' => $project->id,
            'content' => 'Comentario de un usuario bloqueado',
        ]);

        blockContentBetween($viewer, $blocked);

        $this->actingAs($viewer)
            ->postJson(route('comments.like', $comment))
            ->assertForbidden();
    });

    it('no notifica menciones a usuarios bloqueados', function () {
        $author = blockContentUser();
        $mentioned = blockContentUser();
        $project = blockContentProject($author);

        blockContentBetween($author, $mentioned);

        $this->actingAs($author)
            ->postJson(route('comments.store', $project), [
                'content' => '@'.$mentioned->username.' mira esto',
            ])
            ->assertOk();

        expect(Notification::where('user_id', $mentioned->id)
            ->where('type', Notification::TYPE_MENTION)
            ->count())->toBe(0);
    });

    it('permite bloquear y desbloquear desde el perfil', function () {
        $viewer = blockContentUser();
        $target = blockContentUser();

        $this->actingAs($viewer)
            ->postJson(route('users.block', $target))
            ->assertOk();

        expect($viewer->fresh()->isBlockedWithId($target->id))->toBeTrue();

        $this->actingAs($viewer)
            ->get(route('blocked.index'))
            ->assertOk()
            ->assertSee($target->name);

        $this->actingAs($viewer)
            ->get(route('privacy.index'))
            ->assertOk()
            ->assertSee(route('blocked.index'), false);

        $this->actingAs($viewer)
            ->deleteJson(route('users.unblock', $target))
            ->assertOk();

        expect($viewer->fresh()->isBlockedWithId($target->id))->toBeFalse();
    });
});
