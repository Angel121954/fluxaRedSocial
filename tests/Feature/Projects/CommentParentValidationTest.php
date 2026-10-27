<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Respuestas a comentarios', function () {
    it('rechaza responder a un comentario de otro proyecto', function () {
        $commenter = User::factory()->create(['onboarding_completed' => true]);
        $project = Project::factory()->public()->create();
        $otherProject = Project::factory()->public()->create();
        $foreignComment = Comment::create([
            'user_id' => $otherProject->user_id,
            'project_id' => $otherProject->id,
            'content' => 'Comentario de otro proyecto',
        ]);

        $this->actingAs($commenter)
            ->postJson(route('comments.store', $project), [
                'content' => 'Respuesta cruzada',
                'parent_id' => $foreignComment->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('parent_id');

        expect(Comment::where('project_id', $project->id)->count())->toBe(0);
    });

    it('permite responder a un comentario del mismo proyecto', function () {
        $commenter = User::factory()->create(['onboarding_completed' => true]);
        $project = Project::factory()->public()->create();
        $parentComment = Comment::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'content' => 'Comentario original',
        ]);

        $this->actingAs($commenter)
            ->postJson(route('comments.store', $project), [
                'content' => 'Respuesta válida',
                'parent_id' => $parentComment->id,
            ])
            ->assertOk();

        expect(Comment::where('project_id', $project->id)
            ->where('parent_id', $parentComment->id)
            ->exists())->toBeTrue();
    });
});
