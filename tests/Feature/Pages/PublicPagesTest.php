<?php

declare(strict_types=1);

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Páginas públicas', function () {
    it('permite ver las páginas legales sin autenticación', function (string $routeName) {
        $this->get(route($routeName))->assertOk();
    })->with(['about-fluxa', 'terms', 'privacy.policy', 'contact.index']);

    it('incluye las páginas legales en el sitemap', function () {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('about-fluxa'), false)
            ->assertSee(route('terms'), false)
            ->assertSee(route('privacy.policy'), false)
            ->assertSee(route('contact.index'), false)
            ->assertSee(route('salaries.index'), false);
    });

    it('permite enviar el formulario de contacto sin autenticación', function () {
        $this->post(route('contact.store'), [
            'name' => 'Ana Dev',
            'email' => 'ana@example.com',
            'message' => 'Hola, quiero saber más de Fluxa.',
        ])->assertRedirect(route('contact.index'));

        expect(Contact::count())->toBe(1);
    });
});
