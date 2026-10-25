<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use App\Models\UserBlock;
use App\Policies\ProfilePolicy;
use App\Policies\ProjectPolicy;
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function blockTestUser(): User
{
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'activo',
        'onboarding_completed' => true,
        'account_type' => 'personal',
        'email_verified_at' => now(),
    ]);

    Profile::factory()->for($user)->create([
        'visibility' => 'public',
        'accept_messages' => true,
        'avatar' => null,
    ]);

    return $user->load('profile');
}

function blockTestBetween(User $blocker, User $blocked): void
{
    UserBlock::create([
        'blocker_id' => $blocker->id,
        'blocked_id' => $blocked->id,
    ]);
}

describe('Bloqueos', function () {
    it('niega el perfil público en ambas direcciones del bloqueo', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();

        blockTestBetween($owner, $viewer);

        expect((new ProfilePolicy)->view($viewer, $owner->profile))->toBeFalse();

        UserBlock::query()->delete();
        blockTestBetween($viewer, $owner);

        expect((new ProfilePolicy)->view($viewer, $owner->profile))->toBeFalse();
    });

    it('permite al dueño ver su propio perfil aunque exista un bloqueo', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();

        blockTestBetween($owner, $viewer);

        expect((new ProfilePolicy)->view($owner, $owner->profile))->toBeTrue();
    });

    it('niega proyectos públicos y de seguidores si hay bloqueo', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();
        $project = Project::factory()->public()->create(['user_id' => $owner->id]);

        blockTestBetween($owner, $viewer);

        expect((new ProjectPolicy)->view($viewer, $project))->toBeFalse();

        UserBlock::query()->delete();
        $project->update(['privacy' => 'followers']);
        $viewer->follows()->attach($owner->id);

        expect((new ProjectPolicy)->view($viewer, $project))->toBeTrue();

        blockTestBetween($owner, $viewer);

        expect((new ProjectPolicy)->view($viewer, $project))->toBeFalse();
    });

    it('impide seguir a un usuario bloqueado', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->post(route('users.follow', $owner))
            ->assertForbidden();
    });

    it('impide dar like a un proyecto bloqueado', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();
        $project = Project::factory()->public()->create(['user_id' => $owner->id]);

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->post(route('projects.like', $project))
            ->assertForbidden();
    });

    it('impide comentar un proyecto bloqueado', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();
        $project = Project::factory()->public()->create(['user_id' => $owner->id]);

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->postJson(route('comments.store', $project), ['content' => 'Comentario de prueba'])
            ->assertForbidden();
    });

    it('impide dar like a un comentario de un proyecto bloqueado', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();
        $author = blockTestUser();
        $project = Project::factory()->public()->create(['user_id' => $owner->id]);
        $comment = Comment::create([
            'user_id' => $author->id,
            'project_id' => $project->id,
            'content' => 'Comentario de prueba',
        ]);

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->post(route('comments.like', $comment))
            ->assertForbidden();
    });

    it('impide crear una conversación con un usuario bloqueado', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->getJson(route('messages.user', $owner->username))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->get(route('messages.chat', $owner->username))
            ->assertRedirect(route('messages.index'))
            ->assertSessionHas('error');
    });

    it('excluye usuarios bloqueados del buscador de mensajes', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->getJson(route('users.search', ['q' => $owner->username]))
            ->assertOk()
            ->assertJsonMissing(['id' => $owner->id]);
    });

    it('excluye proyectos de usuarios bloqueados del explore', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();
        Project::factory()->public()->create([
            'user_id' => $owner->id,
            'title' => 'Proyecto bloqueado en explore',
        ]);

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->get(route('explore.trending'))
            ->assertOk()
            ->assertDontSee('Proyecto bloqueado en explore');
    });

    it('excluye proyectos de usuarios bloqueados del feed', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();
        $viewer->follows()->attach($owner->id);

        Project::factory()->public()->create([
            'user_id' => $owner->id,
            'title' => 'Proyecto bloqueado en feed',
        ]);

        blockTestBetween($owner, $viewer);

        $this->actingAs($viewer)
            ->get(route('feed.index'))
            ->assertOk()
            ->assertDontSee('Proyecto bloqueado en feed');
    });

    it('elimina los follows en ambas direcciones al bloquear', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();

        $owner->follows()->attach($viewer->id);
        $viewer->follows()->attach($owner->id);

        app(MessageService::class)->blockUser($owner->id, $viewer->id);

        expect($owner->follows()->where('followed_id', $viewer->id)->exists())->toBeFalse()
            ->and($viewer->follows()->where('followed_id', $owner->id)->exists())->toBeFalse();
    });

    it('no permite enviar mensajes en ninguna dirección tras el bloqueo', function () {
        $owner = blockTestUser();
        $viewer = blockTestUser();
        $service = app(MessageService::class);

        blockTestBetween($owner, $viewer);

        expect($service->canSendMessage($owner->id, $viewer->id))->toBeFalse()
            ->and($service->canSendMessage($viewer->id, $owner->id))->toBeFalse();
    });
});
