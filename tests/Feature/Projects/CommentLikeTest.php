<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

describe('Like de comentarios', function () {
    it('alterna el like sin exponer detalles internos', function () {
        Event::fake();
        Queue::fake();

        $owner = User::factory()->create(['onboarding_completed' => true]);
        $viewer = User::factory()->create(['onboarding_completed' => true]);

        Profile::factory()->for($owner)->create(['visibility' => 'public']);
        Profile::factory()->for($viewer)->create(['visibility' => 'public']);

        $project = Project::factory()->public()->create(['user_id' => $owner->id]);

        $comment = Comment::create([
            'user_id' => $owner->id,
            'project_id' => $project->id,
            'content' => 'Comentario de prueba',
        ]);

        $response = $this->actingAs($viewer)
            ->postJson(route('comments.like', $comment));

        $response->assertOk()
            ->assertJson(['success' => true, 'liked' => true]);

        expect(array_keys($response->json()))->toBe(['success', 'liked']);

        $response = $this->actingAs($viewer)
            ->postJson(route('comments.like', $comment));

        $response->assertOk()
            ->assertJson(['success' => true, 'liked' => false]);
    });
});
