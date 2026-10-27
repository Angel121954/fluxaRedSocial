<?php

declare(strict_types=1);

use App\Models\Credential;
use App\Models\Education;
use App\Models\Profile;
use App\Models\Technology;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function credentialTestUser(): User
{
    $user = User::factory()->create(['onboarding_completed' => true]);
    Profile::factory()->for($user)->create(['visibility' => 'public']);

    return $user;
}

function validCredentialPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'AWS Certified Cloud Practitioner',
        'issuer' => 'Amazon Web Services',
        'type' => 'certification',
        'issued_at' => now()->subMonth()->toDateString(),
        'expires_at' => now()->addYear()->toDateString(),
        'credential_id' => 'AWS-123',
        'credential_url' => 'https://example.com/verify/aws-123',
        'description' => 'Certificación de fundamentos de cloud.',
        'privacy' => 'public',
    ], $overrides);
}

describe('Certificaciones del perfil', function () {
    it('crea una certificación para el usuario autenticado', function () {
        $user = credentialTestUser();

        $this->actingAs($user)
            ->post(route('credentials.store'), validCredentialPayload())
            ->assertRedirect(route('credentials.index'));

        $this->assertDatabaseHas('credentials', [
            'user_id' => $user->id,
            'title' => 'AWS Certified Cloud Practitioner',
            'privacy' => 'public',
        ]);
    });

    it('muestra la página de gestión de certificaciones', function () {
        $user = credentialTestUser();

        Credential::create(validCredentialPayload([
            'user_id' => $user->id,
            'title' => 'Certificación visible en gestión',
        ]));

        $this->actingAs($user)
            ->get(route('credentials.index'))
            ->assertOk()
            ->assertSee('Certificaciones')
            ->assertSee('Certificación visible en gestión');
    });

    it('rechaza una certificación con un tipo inválido', function () {
        $user = credentialTestUser();

        $this->actingAs($user)
            ->post(route('credentials.store'), validCredentialPayload(['type' => 'inventado']))
            ->assertSessionHasErrors('type');
    });

    it('rechaza un enlace de verificación que no sea http o https', function () {
        $user = credentialTestUser();

        $this->actingAs($user)
            ->post(route('credentials.store'), validCredentialPayload(['credential_url' => 'javascript:alert(1)']))
            ->assertSessionHasErrors('credential_url');
    });

    it('solo permite actualizar una certificación al dueño', function () {
        $owner = credentialTestUser();
        $other = credentialTestUser();
        $credential = Credential::create(validCredentialPayload(['user_id' => $owner->id]));

        $this->actingAs($other)
            ->put(route('credentials.update', $credential), validCredentialPayload())
            ->assertForbidden();
    });

    it('solo permite eliminar una certificación al dueño', function () {
        $owner = credentialTestUser();
        $other = credentialTestUser();
        $credential = Credential::create(validCredentialPayload(['user_id' => $owner->id]));

        $this->actingAs($other)
            ->delete(route('credentials.destroy', $credential))
            ->assertForbidden();
    });

    it('muestra las certificaciones públicas y oculta las privadas a otros usuarios', function () {
        $owner = credentialTestUser();
        $viewer = credentialTestUser();

        Credential::create(validCredentialPayload([
            'user_id' => $owner->id,
            'title' => 'Certificación Pública',
            'privacy' => 'public',
        ]));
        Credential::create(validCredentialPayload([
            'user_id' => $owner->id,
            'title' => 'Certificación Privada',
            'privacy' => 'private',
        ]));

        $this->actingAs($viewer)
            ->get(route('profile.show', $owner->username))
            ->assertOk()
            ->assertSee('Certificación Pública')
            ->assertDontSee('Certificación Privada');
    });

    it('muestra las certificaciones públicas a invitados y oculta las privadas', function () {
        $owner = credentialTestUser();

        Credential::create(validCredentialPayload([
            'user_id' => $owner->id,
            'title' => 'Certificación Pública Invitado',
            'privacy' => 'public',
        ]));
        Credential::create(validCredentialPayload([
            'user_id' => $owner->id,
            'title' => 'Certificación Privada Invitado',
            'privacy' => 'private',
        ]));

        $this->get(route('profile.show', $owner->username))
            ->assertOk()
            ->assertSee('Certificación Pública Invitado')
            ->assertDontSee('Certificación Privada Invitado');
    });

    it('muestra las certificaciones privadas al dueño del perfil', function () {
        $owner = credentialTestUser();

        Credential::create(validCredentialPayload([
            'user_id' => $owner->id,
            'title' => 'Certificación Privada',
            'privacy' => 'private',
        ]));

        $this->actingAs($owner)
            ->get(route('profile.show', $owner->username))
            ->assertOk()
            ->assertSee('Certificación Privada')
            ->assertSee('Privado');
    });

    it('muestra experiencia y educación en la trayectoria pública', function () {
        $owner = credentialTestUser();
        $viewer = credentialTestUser();

        $technology = Technology::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'icon' => 'laravel',
            'category' => 'framework',
        ]);

        $experience = WorkExperience::create([
            'user_id' => $owner->id,
            'type' => 'formal',
            'company' => 'Acme',
            'position' => 'Desarrollador Backend',
            'work_mode' => 'remote',
            'started_at' => now()->subYear()->toDateString(),
            'current' => true,
        ]);
        $experience->technologies()->attach($technology);

        Education::create([
            'user_id' => $owner->id,
            'institution' => 'Universidad Nacional',
            'degree' => 'Ingeniería de Software',
            'graduated_year' => 2025,
            'current' => false,
        ]);

        $this->actingAs($viewer)
            ->get(route('profile.show', $owner->username))
            ->assertOk()
            ->assertSee('Trayectoria')
            ->assertSee('Desarrollador Backend')
            ->assertSee('Acme')
            ->assertSee('Laravel')
            ->assertSee('Ingeniería de Software')
            ->assertSee('Universidad Nacional');
    });

    it('oculta la pestaña de trayectoria cuando no hay datos y no es el dueño', function () {
        $owner = credentialTestUser();
        $viewer = credentialTestUser();

        $this->actingAs($viewer)
            ->get(route('profile.show', $owner->username))
            ->assertOk()
            ->assertDontSee('Trayectoria');
    });
});
