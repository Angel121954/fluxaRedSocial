<?php

declare(strict_types=1);

namespace App\Services\Cv;

use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CvDataService
{
    public const DEFAULT_CV_SETTINGS = [
        'format' => 'pdf',
        'show_photo' => true,
        'show_location' => true,
        'show_email' => true,
        'show_projects' => true,
        'show_experience' => true,
        'show_education' => true,
        'section_order' => ['experience', 'projects', 'education', 'skills'],
    ];

    public function prepare(User $user, ?array $cvSettings = null): array
    {
        $profile = $user->profile;
        $cvSettings = $cvSettings
            ? array_merge(self::DEFAULT_CV_SETTINGS, $cvSettings)
            : ($profile->cv_settings
                ? array_merge(self::DEFAULT_CV_SETTINGS, $profile->cv_settings)
                : self::DEFAULT_CV_SETTINGS);

        $urlPerfil = request()->getHost() === 'localhost'
            ? 'profile/'.$user->username
            : request()->getHost().'/profile/'.$user->username;
        $urlQrExterno = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
            .urlencode('https://'.$urlPerfil)
            .'&color=0d9488&bgcolor=ffffff&margin=6';

        $followersCount = $user->followers()->count();
        $followingCount = $user->follows()->count();
        $technologies = $this->loadTechnologyIcons($user);

        $projects = collect();

        if ($cvSettings['show_projects'] ?? true) {
            $query = $user->projects()->with(['media', 'technologies']);

            $selectedIds = $cvSettings['selected_project_ids'] ?? [];

            if (! empty($selectedIds)) {
                $query->whereIn('id', $selectedIds);
            } else {
                $query->where('privacy', 'public')->latest();
            }

            $projects = $query->get();

            if (! empty($selectedIds)) {
                $projects = collect($selectedIds)
                    ->map(fn (int $id) => $projects->firstWhere('id', $id))
                    ->filter()
                    ->values();
            }
        }

        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();

        $avatarBase64 = Cache::store('redis')->remember('cv_avatar_'.$user->id, 3600, fn () => $this->urlToBase64($profile->avatar)
        );

        $logoBase64 = Cache::store('redis')->rememberForever('cv_logo_base64', fn () => 'data:image/png;base64,'.base64_encode(
            file_get_contents(public_path('img/logo.png'))
        )
        );

        $qrBase64 = Cache::store('redis')->remember('cv_qr_'.$user->id, 86400, fn () => $this->generateQrCode('https://'.$urlPerfil)
        );

        $rolProfesional = $user->role
            ? ucfirst($user->role).' Developer'
            : 'Software Developer';

        $estadisticas = [
            ['valor' => $projects->count(), 'etiqueta' => 'Proyectos'],
            ['valor' => $followingCount, 'etiqueta' => 'Siguiendo'],
            ['valor' => $followersCount, 'etiqueta' => 'Seguidores'],
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
            'rolProfesional' => $rolProfesional,
            'estadisticas' => $estadisticas,
            'srcAvatar' => $srcAvatar,
            'srcLogo' => $logoBase64,
            'srcQr' => $qrBase64 ?? $urlQrExterno,
        ];
    }

    public function urlToBase64(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        try {
            $content = str_starts_with($url, 'http')
                ? file_get_contents($url)
                : file_get_contents(public_path('storage/'.$url));

            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($content);

            return "data:{$mime};base64,".base64_encode($content);
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

            return 'data:image/svg+xml;base64,'.base64_encode($svg);
        } catch (Exception $e) {
            Log::warning('CV: error generando QR', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function loadTechnologyIcons(User $user): Collection
    {
        return $user->technologies()->orderBy('name')->get()
            ->map(function ($tech) {
                $tech->iconoB64 = Cache::store('redis')->remember('cv_tech_icon_'.$tech->id, 86400, function () use ($tech) {
                    try {
                        $url = $tech->iconUrl();

                        return 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($url));
                    } catch (Exception $e) {
                        return null;
                    }
                });

                return $tech;
            });
    }
}
