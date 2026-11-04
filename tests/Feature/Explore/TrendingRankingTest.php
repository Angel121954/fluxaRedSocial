<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function trendingViewer(): User
{
    return User::factory()->create(['onboarding_completed' => true]);
}

function ageProject(Project $project, int $days): void
{
    DB::table('projects')->where('id', $project->id)->update([
        'created_at' => now()->subDays($days),
    ]);
}

describe('Ranking de tendencias', function () {
    it('prioriza un proyecto reciente con menos interacción sobre uno antiguo con muchas', function () {
        $viewer = trendingViewer();
        $author = User::factory()->create();

        $old = Project::factory()->public()->for($author)->create([
            'title' => 'Proyecto antiguo',
            'likes_count' => 200,
            'comments_count' => 20,
        ]);
        ageProject($old, 60);

        Project::factory()->public()->for($author)->create([
            'title' => 'Proyecto reciente',
            'likes_count' => 5,
            'comments_count' => 1,
        ]);

        $this->actingAs($viewer)
            ->get(route('explore.trending'))
            ->assertOk()
            ->assertSeeInOrder(['Proyecto reciente', 'Proyecto antiguo']);
    });

    it('excluye proyectos de usuarios baneados', function () {
        $viewer = trendingViewer();
        $banned = User::factory()->create(['status' => 'banned']);

        Project::factory()->public()->for($banned)->create(['title' => 'Proyecto baneado']);

        $this->actingAs($viewer)
            ->get(route('explore.trending'))
            ->assertOk()
            ->assertDontSee('Proyecto baneado');
    });
});
