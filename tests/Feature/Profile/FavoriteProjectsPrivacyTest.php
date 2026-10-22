<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Privacidad de proyectos favoritos', function () {
    it('no muestra proyectos privados en los favoritos de otro perfil', function () {
        $owner = User::factory()->create();
        Profile::factory()->for($owner)->create([
            'visibility' => 'public',
            'show_favorites' => true,
        ]);

        $viewer = User::factory()->create();

        $private = Project::factory()->private()->create([
            'user_id' => $viewer->id,
            'title' => 'Proyecto Privado Favorito XYZ',
        ]);
        $public = Project::factory()->public()->create([
            'user_id' => $viewer->id,
            'title' => 'Proyecto Publico Favorito XYZ',
        ]);

        $owner->bookmarkedProjects()->attach([$private->id, $public->id]);

        $this->actingAs($viewer)
            ->get(route('profile.show', $owner->username))
            ->assertOk()
            ->assertSee('Proyecto Publico Favorito XYZ')
            ->assertDontSee('Proyecto Privado Favorito XYZ');
    });
});
