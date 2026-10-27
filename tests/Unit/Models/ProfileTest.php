<?php

declare(strict_types=1);

use App\Models\Profile;

it('oculta los datos privados al serializar el perfil', function () {
    $profile = new Profile([
        'bio' => 'Desarrolladora backend',
        'phone_code' => '+57',
        'phone_number' => '3001234567',
        'birth_date' => '1995-01-01',
        'gender' => 'female',
        'latitude' => '4.6097100',
        'longitude' => '-74.0817500',
        'cv_settings' => ['show_phone' => true],
    ]);

    $data = $profile->toArray();

    expect($data)->not->toHaveKeys([
        'phone_code',
        'phone_number',
        'birth_date',
        'gender',
        'latitude',
        'longitude',
        'cv_settings',
    ])->and($data['bio'])->toBe('Desarrolladora backend');
});
