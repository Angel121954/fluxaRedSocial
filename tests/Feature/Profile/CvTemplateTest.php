<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;

describe('Plantilla del CV', function () {
    it('no incluye métricas sociales del perfil', function () {
        $user = new User([
            'name' => 'Ana Dev',
            'username' => 'ana-dev',
            'email' => 'ana@example.com',
        ]);
        $profile = new Profile([
            'specialty' => 'backend',
            'city' => 'Bogotá',
            'country' => 'Colombia',
        ]);
        $user->setRelation('profile', $profile);

        $html = view('components.cv-template', [
            'profile' => $profile,
            'user' => $user,
            'technologies' => collect(),
            'projects' => collect(),
            'workExperiences' => collect(),
            'educations' => collect(),
            'cvSettings' => [
                'show_photo' => false,
                'show_location' => true,
                'show_email' => false,
                'show_phone' => false,
                'show_projects' => false,
                'show_experience' => false,
                'show_education' => false,
                'section_order' => [],
            ],
        ])->render();

        expect($html)->not->toContain('seguidores')
            ->and($html)->not->toContain('siguiendo')
            ->and($html)->not->toContain('Miembro desde');
    });
});
