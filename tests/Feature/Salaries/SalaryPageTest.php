<?php

declare(strict_types=1);

use App\Models\SalaryReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Página pública de sueldos', function () {
    it('permite ver la página y sus datos sin autenticación', function () {
        $this->get(route('salaries.index'))
            ->assertOk()
            ->assertSee('Sueldos del developer LATAM');

        $this->getJson(route('salaries.data'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    });

    it('no permite aportar un sueldo sin autenticación', function () {
        $this->post(route('salaries.store'), [
            'country' => 'Colombia',
            'seniority' => 'mid',
            'experience_years' => 3,
            'salary_usd' => 2500,
            'modality' => 'remote',
        ])->assertRedirect(route('login'));
    });

    it('permite a un usuario autenticado aportar su sueldo', function () {
        $user = User::factory()->create(['onboarding_completed' => true]);

        $this->actingAs($user)
            ->postJson(route('salaries.store'), [
                'country' => 'Colombia',
                'city' => 'Bogotá',
                'seniority' => 'mid',
                'experience_years' => 3,
                'salary_usd' => 2500,
                'modality' => 'remote',
                'company' => 'Fluxa',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        expect(SalaryReport::query()
            ->where('user_id', $user->id)
            ->where('salary_usd', 2500)
            ->exists())->toBeTrue();
    });
});
