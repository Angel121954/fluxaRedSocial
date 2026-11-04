<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Reportar proyecto', function () {
    it('impide reportar un proyecto propio', function () {
        $owner = User::factory()->create(['onboarding_completed' => true]);
        $project = Project::factory()->public()->for($owner)->create();

        $this->actingAs($owner)
            ->postJson(route('projects.report', $project), [
                'reason' => 'Quiero reportar mi propio proyecto',
            ])
            ->assertForbidden();

        expect(ProjectReport::count())->toBe(0);
    });

    it('permite reportar el proyecto de otro usuario', function () {
        $reporter = User::factory()->create(['onboarding_completed' => true]);
        $project = Project::factory()->public()->create();

        $this->actingAs($reporter)
            ->postJson(route('projects.report', $project), [
                'reason' => 'El proyecto contiene contenido inapropiado',
            ])
            ->assertOk();

        expect(ProjectReport::where('user_id', $reporter->id)
            ->where('project_id', $project->id)
            ->exists())->toBeTrue();
    });

    it('no duplica el reporte del mismo usuario', function () {
        $reporter = User::factory()->create(['onboarding_completed' => true]);
        $project = Project::factory()->public()->create();

        $this->actingAs($reporter)
            ->postJson(route('projects.report', $project), [
                'reason' => 'El proyecto contiene contenido inapropiado',
            ])
            ->assertOk();

        $this->actingAs($reporter)
            ->postJson(route('projects.report', $project), [
                'reason' => 'El proyecto contiene contenido inapropiado',
            ])
            ->assertOk();

        expect(ProjectReport::count())->toBe(1);
    });
});
