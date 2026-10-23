<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Límites de escritura', function () {
    it('limita los follows a 30 por minuto por usuario', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);
        $target = User::factory()->create(['onboarding_completed' => true]);

        for ($i = 0; $i < 30; $i++) {
            $this->actingAs($user)
                ->postJson(route('users.follow', $target))
                ->assertOk();
        }

        $this->actingAs($user)
            ->postJson(route('users.follow', $target))
            ->assertStatus(429);
    });

    it('limita los comentarios a 20 por minuto por usuario', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);
        $project = Project::factory()->public()->create();

        for ($i = 0; $i < 20; $i++) {
            $this->actingAs($user)
                ->postJson(route('comments.store', $project), ['content' => "Comentario {$i}"])
                ->assertOk();
        }

        $this->actingAs($user)
            ->postJson(route('comments.store', $project), ['content' => 'Comentario 21'])
            ->assertStatus(429);
    });
});
