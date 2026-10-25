<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function administradorDashboard(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'status' => 'activo',
        'onboarding_completed' => 'true',
        'account_type' => 'personal',
    ]);
}

describe('Panel de administración', function () {
    it('muestra el dashboard a un administrador', function () {
        $administrador = administradorDashboard();

        $this->actingAs($administrador)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Panel Administrativo')
            ->assertSee('Estado de la comunidad')
            ->assertSee('Todo el tiempo');
    });

    it('filtra las métricas por rango de fechas', function () {
        $administrador = administradorDashboard();

        User::factory()->create([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        User::factory()->create([
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $this->actingAs($administrador)
            ->get(route('admin.dashboard', [
                'desde' => now()->subDays(5)->format('Y-m-d'),
                'hasta' => now()->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertViewHas('totalUsuarios', 2)
            ->assertViewHas('variaciones');
    });
});
