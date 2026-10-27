<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('muestra solo proyectos públicos raíz con publicRoot', function () {
    $author = User::factory()->create();
    $publicProject = Project::factory()->public()->for($author)->create();
    $privateProject = Project::factory()->private()->for($author)->create();
    $reply = Project::factory()->public()->for($author)->create([
        'parent_id' => $publicProject->id,
    ]);

    $ids = Project::publicRoot()->pluck('id')->map(fn ($id) => (int) $id);

    expect($ids)
        ->toContain($publicProject->id)
        ->not->toContain($privateProject->id)
        ->not->toContain($reply->id);
});

it('excluye proyectos de usuarios bloqueados en ambos sentidos', function () {
    $viewer = User::factory()->create();
    $blockedAuthor = User::factory()->create();
    $blockerAuthor = User::factory()->create();

    $visibleProject = Project::factory()->public()->for(User::factory())->create();
    $blockedProject = Project::factory()->public()->for($blockedAuthor)->create();
    $blockerProject = Project::factory()->public()->for($blockerAuthor)->create();

    $viewer->blockedUsers()->attach($blockedAuthor->id);
    $blockerAuthor->blockedUsers()->attach($viewer->id);

    $ids = Project::publicRoot()
        ->excludingBlocked($viewer->blockedUserIds())
        ->pluck('id')
        ->map(fn ($id) => (int) $id);

    expect($ids)
        ->toContain($visibleProject->id)
        ->not->toContain($blockedProject->id)
        ->not->toContain($blockerProject->id);
});
