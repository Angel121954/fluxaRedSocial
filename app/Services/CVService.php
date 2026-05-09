<?php

namespace App\Services;

use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class CVService
{
    private const ICON_EXCEPTIONS = [
        'amazonwebservices' => 'plain-wordmark',
        'angularjs' => 'plain',
        'django' => 'plain',
        'tailwindcss' => 'plain',
        'kubernetes' => 'plain',
        'graphql' => 'plain',
        'firebase' => 'plain',
        'express' => 'original-wordmark',
    ];

    private const DEFAULT_CV_SETTINGS = [
        'template' => 'classic',
        'show_photo' => true,
        'show_location' => true,
        'show_email' => true,
        'show_projects' => true,
        'show_experience' => true,
        'show_education' => true,
        'section_order' => ['experience', 'projects', 'education'],
    ];

    public function prepareCvData(User $user, ?array $cvSettings = null): array
    {
        $profile = $user->profile;
        $cvSettings = $cvSettings
            ? array_merge(self::DEFAULT_CV_SETTINGS, $cvSettings)
            : ($profile->cv_settings
                ? array_merge(self::DEFAULT_CV_SETTINGS, $profile->cv_settings)
                : self::DEFAULT_CV_SETTINGS);

        $urlPerfil = request()->getHost() . '/' . $user->username;
        $urlQrExterno = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
            . urlencode('https://' . $urlPerfil)
            . '&color=0d9488&bgcolor=ffffff&margin=6';

        $followersCount = $user->followers()->count();
        $followingCount = $user->follows()->count();
        $daysActive = (int) ($profile->days_active ?? 0);

        $technologies = $this->loadTechnologyIcons($user);

        $projects = ($cvSettings['show_projects'] ?? true)
            ? $user->projects()
            ->with(['media', 'technologies', 'likes', 'bookmarks', 'skillEndorsements'])
            ->where('privacy', 'public')
            ->latest()
            ->get()
            : collect();

        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();
        $avatarBase64 = $this->urlToBase64($profile->avatar);
        $logoBase64 = 'data:image/png;base64,' . base64_encode(
            file_get_contents(public_path('img/logoFluxa.png'))
        );
        $qrBase64 = $this->generateQrCode('https://' . $urlPerfil);

        $rolProfesional = $technologies->isNotEmpty()
            ? $technologies->first()->name . ' Developer'
            : 'Software Developer';

        $estadisticas = [
            ['valor' => $projects->count(), 'etiqueta' => 'Proyectos'],
            ['valor' => $followingCount, 'etiqueta' => 'Siguiendo'],
            ['valor' => $followersCount, 'etiqueta' => 'Seguidores'],
            ['valor' => $daysActive, 'etiqueta' => 'Días activo'],
        ];

        $srcAvatar = $avatarBase64 ?? ($profile->avatar ? str_replace('type=normal', 'type=large', $profile->avatar) : '');

        return [
            'profile' => $profile,
            'user' => $user,
            'technologies' => $technologies,
            'projects' => $projects,
            'workExperiences' => $workExperiences,
            'educations' => $educations,
            'avatarBase64' => $avatarBase64,
            'logoBase64' => $logoBase64,
            'qrBase64' => $qrBase64,
            'cvSettings' => $cvSettings,
            'urlPerfil' => $urlPerfil,
            'urlQrExterno' => $urlQrExterno,
            'cantidadSeguidores' => $followersCount,
            'cantidadSiguiendo' => $followingCount,
            'diasActivo' => $daysActive,
            'rolProfesional' => $rolProfesional,
            'estadisticas' => $estadisticas,
            'srcAvatar' => $srcAvatar,
            'srcLogo' => $logoBase64,
            'srcQr' => $qrBase64 ?? $urlQrExterno,
        ];
    }

    public function generatePdf(string $html): string
    {
        return Browsershot::html($html)
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
    }

    public function urlToBase64(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        try {
            $content = str_starts_with($url, 'http')
                ? file_get_contents($url)
                : file_get_contents(public_path('storage/' . $url));

            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($content);

            return "data:{$mime};base64," . base64_encode($content);
        } catch (Exception $e) {
            Log::warning('CV: no se pudo convertir imagen a base64', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function generateQrCode(string $data): ?string
    {
        try {
            $qrCode = new QrCode(
                data: $data,
                size: 150,
                margin: 0,
            );

            $writer = new SvgWriter;
            $svg = $writer->write($qrCode)->getString();

            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (Exception $e) {
            Log::warning('CV: error generando QR', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function loadTechnologyIcons($user): Collection
    {
        return $user->technologies()->orderBy('name')->get()
            ->map(function ($tech) {
                $slug = (string) $tech->slug;
                $tipo = self::ICON_EXCEPTIONS[$slug] ?? 'original';
                $url = "https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{$slug}/{$slug}-{$tipo}.svg";

                try {
                    $tech->iconoB64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($url));
                } catch (Exception $e) {
                    $tech->iconoB64 = null;
                }

                return $tech;
            });
    }
}
