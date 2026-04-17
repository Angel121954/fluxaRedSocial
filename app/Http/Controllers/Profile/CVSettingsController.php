<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCVSettingsRequest;
use Exception;
use Spatie\Browsershot\Browsershot;

class CVSettingsController extends Controller
{
    public function edit()
    {
        $cvSettings = auth()->user()->profile->cv_settings ?? [
            'template' => 'classic',
            'show_photo' => true,
            'show_location' => true,
            'show_email' => true,
            'show_projects' => true,
            'show_experience' => true,
            'show_education' => true,
            'section_order' => ['experience', 'projects', 'education', 'skills'],
        ];

        return view('profile.cv', compact('cvSettings'));
    }

    public function update(UpdateCVSettingsRequest $request)
    {
        $profile = $request->user()->profile;
        $validated = $request->validated();

        $sectionOrder = $request->input('section_order', ['experience', 'projects', 'education', 'skills']);

        $cvSettings = [
            'template' => $validated['template'],
            'show_photo' => $request->boolean('show_photo'),
            'show_location' => $request->boolean('show_location'),
            'show_email' => $request->boolean('show_email'),
            'show_projects' => $request->boolean('show_projects'),
            'show_experience' => $request->boolean('show_experience'),
            'show_education' => $request->boolean('show_education'),
            'section_order' => $sectionOrder,
        ];

        $profile->cv_settings = $cvSettings;
        $profile->save();

        return redirect()
            ->back()
            ->with('success', 'Configuración del CV actualizada correctamente.');
    }

    /**
     * Restaurar configuración del CV a valores por defecto
     */
    public function restore()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $profile->cv_settings = null;
        $profile->save();

        return redirect()
            ->route('cv.edit')
            ->with('success', 'Configuración del CV restaurada correctamente.');
    }

    /**
     * Generar y descargar el CV en PDF con Browsershot
     */
    public function download()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $cvSettings = $profile->cv_settings ?? [
            'template' => 'classic',
            'show_photo' => true,
            'show_location' => true,
            'show_email' => true,
            'show_projects' => true,
            'show_experience' => true,
            'show_education' => true,
            'section_order' => ['experience', 'projects', 'education', 'skills'],
        ];

        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();

        // FIX 4: aplicar excepciones de icono igual que ProfileController::cargarIconosTecnologias()
        $excepcionesIcono = [
            'amazonwebservices' => 'plain-wordmark',
            'angularjs' => 'plain',
            'django' => 'plain',
            'tailwindcss' => 'plain',
            'kubernetes' => 'plain',
            'graphql' => 'plain',
            'firebase' => 'plain',
            'express' => 'original-wordmark',
        ];

        $technologies = $user->technologies()->orderBy('name')->get()
            ->map(function ($tech) use ($excepcionesIcono) {
                $slug = (string) $tech->slug;
                $tipo = $excepcionesIcono[$slug] ?? 'original';
                $url = "https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{$slug}/{$slug}-{$tipo}.svg";
                try {
                    $tech->iconoB64 = 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($url));
                } catch (Exception $e) {
                    $tech->iconoB64 = null;
                }

                return $tech;
            });

        $projects = ($cvSettings['show_projects'] ?? true)
            ? $user->projects()->with('technologies')->latest()->get()
            : collect();

        // FIX 2: detectar MIME real del avatar en lugar de hardcodear image/jpeg
        $avatarBase64 = null;
        if ($profile->avatar) {
            try {
                $avatarUrl = $profile->avatar;
                $contenido = str_starts_with($avatarUrl, 'http')
                    ? file_get_contents($avatarUrl)
                    : file_get_contents(public_path('storage/'.$avatarUrl));

                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contenido);
                $avatarBase64 = "data:{$mime};base64,".base64_encode($contenido);
            } catch (Exception $e) {
                $avatarBase64 = null;
            }
        }

        $urlPerfil = request()->getHost().'/'.$user->username;
        $urlQrExterno = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
            .urlencode('https://'.$urlPerfil)
            .'&color=0d9488&bgcolor=ffffff&margin=6';

        $cantidadSeguidores = $user->followers()->count();
        $cantidadSiguiendo = $user->follows()->count();
        $diasActivo = (int) ($profile->days_active ?? 0);
        $rolProfesional = $technologies->isNotEmpty()
            ? $technologies->first()->name.' Developer'
            : 'Software Developer';

        $estadisticas = [
            ['valor' => $projects->count(),  'etiqueta' => 'Proyectos'],
            ['valor' => $cantidadSiguiendo,  'etiqueta' => 'Siguiendo'],
            ['valor' => $cantidadSeguidores, 'etiqueta' => 'Seguidores'],
            ['valor' => $diasActivo,         'etiqueta' => 'Días activo'],
        ];

        $srcAvatar = $avatarBase64 ?? ($profile->avatar ? str_replace('type=normal', 'type=large', $profile->avatar) : '');

        // FIX 3: logo como base64 para que Browsershot (headless) lo resuelva correctamente
        $srcLogo = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('img/logoFluxa.png')));

        $srcQr = $urlQrExterno;

        $data = compact(
            'profile',
            'user',
            'cvSettings',
            'workExperiences',
            'educations',
            'projects',
            'technologies',
            'avatarBase64',
            'urlPerfil',
            'urlQrExterno',
            'cantidadSeguidores',
            'cantidadSiguiendo',
            'diasActivo',
            'rolProfesional',
            'estadisticas',
            'srcAvatar',
            'srcLogo',
            'srcQr'
        );

        $html = view('components.cv-template', $data)->render();

        // FIX 1: eliminadas $tmpPath y mkdir() — eran código muerto (el PDF se retorna desde memoria)
        $filename = 'cv-'.str($user->username)->slug().'-'.now()->format('Ymd').'.pdf';

        $pdf = Browsershot::html($html)
            ->setNodeBinary('/usr/bin/node')
            ->setNpmBinary('/usr/bin/npm')
            ->setNodeModulePath(env('NODE_MODULES_PATH', '/var/www/html/node_modules'))
            ->setChromePath(env('CHROME_PATH'))
            ->noSandbox()
            ->timeout(300)
            ->setOption('waitUntil', 'domcontentloaded')
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
