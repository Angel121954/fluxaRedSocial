<?php

namespace App\Services;

use App\Models\User;

class CvDataService
{
    public function preparar(User $user, ?array $cvSettings = null): array
    {
        $perfil = $user->profile;
        $usuario = $perfil->user;

        $defaultCvSettings = [
            'show_photo' => true,
            'show_location' => true,
            'show_email' => true,
            'show_projects' => true,
            'show_experience' => true,
            'show_education' => true,
            'section_order' => ['experience', 'projects', 'education'],
        ];

        $cvSettings = $cvSettings
            ? array_merge($defaultCvSettings, $cvSettings)
            : $defaultCvSettings;

        $urlPerfil = request()->getHost().'/'.$usuario->username;
        $urlQrExterno = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
            .urlencode('https://'.$urlPerfil)
            .'&color=0d9488&bgcolor=ffffff&margin=6';

        $cantidadSeguidores = $usuario->followers()->count();
        $cantidadSiguiendo = $usuario->follows()->count();
        $diasActivo = (int) ($perfil->days_active ?? 0);

        $paleta = [
            'fondo' => '#f8fafc',
            'tarjeta' => '#ffffff',
            'primario' => '#14b8a6',
            'primarioOscuro' => '#0d9488',
            'primarioTexto' => '#ffffff',
            'secundario' => '#f0fdfa',
            'borde' => '#e2e8f0',
            'texto' => '#0f172a',
            'textoSuave' => '#64748b',
            'barraLateral' => '#f8fafc',
            'azul' => '#0ea5e9',
            'linkedin' => '#0077b5',
            'twitter' => '#1da1f2',
        ];

        $rolProfesional = $user->technologies->isNotEmpty()
            ? $user->technologies->first()->name.' Developer'
            : 'Software Developer';

        $estadisticas = [
            ['valor' => $user->projects()->count(), 'etiqueta' => 'Proyectos'],
            ['valor' => $cantidadSiguiendo, 'etiqueta' => 'Siguiendo'],
            ['valor' => $cantidadSeguidores, 'etiqueta' => 'Seguidores'],
            ['valor' => $diasActivo, 'etiqueta' => 'Días activo'],
        ];

        $srcAvatar = $perfil->avatar
            ? str_replace('type=normal', 'type=large', $perfil->avatar)
            : '';

        return [
            'perfil' => $perfil,
            'usuario' => $usuario,
            'cvSettings' => $cvSettings,
            'urlPerfil' => $urlPerfil,
            'urlQrExterno' => $urlQrExterno,
            'paleta' => $paleta,
            'rolProfesional' => $rolProfesional,
            'estadisticas' => $estadisticas,
            'srcAvatar' => $srcAvatar,
            'srcLogo' => asset('img/logoFluxa.png'),
            'srcQr' => $urlQrExterno,
        ];
    }
}
