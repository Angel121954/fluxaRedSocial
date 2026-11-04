<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Compartir proyectos', function () {
    it('registra un compartido del proyecto', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);
        $project = Project::factory()->public()->for(User::factory())->create([
            'shares_count' => 0,
        ]);

        $this->actingAs($user)
            ->postJson(route('projects.share', $project))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'shares_count' => 1,
            ]);

        expect($project->fresh()->shares_count)->toBe(1);
    });

    it('no registra compartidos sin autenticación', function () {
        $project = Project::factory()->public()->for(User::factory())->create();

        $this->post(route('projects.share', $project))
            ->assertRedirect(route('login'));
    });
});
