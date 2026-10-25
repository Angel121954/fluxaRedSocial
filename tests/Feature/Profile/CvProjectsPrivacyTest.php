<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use App\Services\Cv\CvDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function cvOwnerWithProjects(): array
{
    $owner = User::factory()->create();

    $publicProject = Project::factory()->public()->create(['user_id' => $owner->id]);
    $privateProject = Project::factory()->private()->create(['user_id' => $owner->id]);

    Profile::factory()->for($owner)->create([
        'visibility' => 'public',
        'avatar' => null,
        'cv_settings' => [
            'show_projects' => true,
            'selected_project_ids' => [$publicProject->id, $privateProject->id],
            'section_order' => ['projects'],
        ],
    ]);

    return [$owner->load('profile'), $publicProject, $privateProject];
}

describe('Privacidad de proyectos en el CV', function () {
    it('excluye los proyectos privados seleccionados cuando el CV lo ve otro usuario', function () {
        [$owner, $publicProject] = cvOwnerWithProjects();
        $viewer = User::factory()->create();

        $data = app(CvDataService::class)->prepare($owner, null, $viewer);

        expect($data['projects']->pluck('id')->all())->toBe([$publicProject->id]);
    });

    it('incluye los proyectos privados seleccionados en el CV del dueño', function () {
        [$owner, $publicProject, $privateProject] = cvOwnerWithProjects();

        $data = app(CvDataService::class)->prepare($owner, null, $owner);

        expect($data['projects']->pluck('id')->all())->toBe([
            $publicProject->id,
            $privateProject->id,
        ]);
    });
});
