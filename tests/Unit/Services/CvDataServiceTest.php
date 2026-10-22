<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use App\Services\Cv\CvDataService;

function cvUser(bool $showEmail): User
{
    $user = new User;
    $user->setRelation('profile', new Profile([
        'show_email' => $showEmail,
        'phone_code' => '+57',
        'phone_number' => '3001234567',
    ]));

    return $user;
}

describe('CvDataService', function () {
    it('oculta el email y el teléfono por defecto', function () {
        $settings = (new CvDataService)->defaultSettings(cvUser(false));

        expect($settings['show_email'])->toBeFalse()
            ->and($settings['show_phone'])->toBeFalse();
    });

    it('usa la preferencia de email del perfil como valor por defecto del CV', function () {
        $settings = (new CvDataService)->defaultSettings(cvUser(true));

        expect($settings['show_email'])->toBeTrue();
    });

    it('oculta el email del CV aunque la configuración guardada lo tenga activo si el perfil lo oculta', function () {
        $settings = (new CvDataService)->settingsFor(cvUser(false), ['show_email' => true]);

        expect($settings['show_email'])->toBeFalse();
    });

    it('permite ocultar el email del CV cuando el perfil lo muestra', function () {
        $settings = (new CvDataService)->settingsFor(cvUser(true), ['show_email' => false]);

        expect($settings['show_email'])->toBeFalse();
    });
});
