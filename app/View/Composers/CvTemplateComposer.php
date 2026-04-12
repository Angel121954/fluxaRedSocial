<?php

namespace App\View\Composers;

use Illuminate\View\View;

class CvTemplateComposer
{
    public function compose(View $view)
    {
        $data = $view->getData();

        $profile = $data['profile'] ?? null;
        $user = $data['user'] ?? null;

        if (! $profile || ! $user) {
            return;
        }

        $urlPerfil = request()->getHost().'/'.$user->username;
        $urlQrExterno = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
            .urlencode('https://'.$urlPerfil)
            .'&color=0d9488&bgcolor=ffffff&margin=6';

        $cantidadSeguidores = $user->followers()->count();
        $cantidadSiguiendo = $user->follows()->count();
        $diasActivo = (int) ($profile->days_active ?? 0);

        $rolProfesional = isset($data['technologies']) && $data['technologies']->isNotEmpty()
            ? $data['technologies']->first()->name.' Developer'
            : 'Software Developer';

        $estadisticas = [
            ['valor' => isset($data['projects']) ? $data['projects']->count() : 0, 'etiqueta' => 'Proyectos'],
            ['valor' => $cantidadSiguiendo, 'etiqueta' => 'Siguiendo'],
            ['valor' => $cantidadSeguidores, 'etiqueta' => 'Seguidores'],
            ['valor' => $diasActivo, 'etiqueta' => 'Días activo'],
        ];

        $srcAvatar = isset($data['avatarBase64']) && $data['avatarBase64']
            ? $data['avatarBase64']
            : ($profile->avatar ? str_replace('type=normal', 'type=large', $profile->avatar) : '');

        $srcLogo = isset($data['logoBase64']) && $data['logoBase64']
            ? $data['logoBase64']
            : asset('img/logoFluxa.png');

        $srcQr = isset($data['qrBase64']) && $data['qrBase64']
            ? $data['qrBase64']
            : $urlQrExterno;

        $view->with(compact(
            'urlPerfil', 'urlQrExterno', 'cantidadSeguidores',
            'cantidadSiguiendo', 'diasActivo', 'rolProfesional',
            'estadisticas', 'srcAvatar', 'srcLogo', 'srcQr'
        ));
    }
}
