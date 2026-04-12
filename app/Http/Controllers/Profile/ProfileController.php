<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Models\Profile;
use App\Models\ProjectLike;
use App\Services\ProfileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $profilePrivate = Profile::where('');
        $user = Auth::user();
        $profile = $user->profile;
        $isOwner = true;

        $projects = $user->projects()
            ->with(['media', 'technologies'])
            ->withCount('media')
            ->latest()
            ->get();

        $likedIds = ProjectLike::where('user_id', $user->id)
            ->whereIn('project_id', $projects->pluck('id'))
            ->pluck('project_id')
            ->toArray();

        $technologies = $user->technologies()->orderBy('name')->get();
        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();
        $followingCount = $user->follows()->count();
        $followersCount = $user->followers()->count();

        return view('profile.index', compact('user', 'profile', 'projects', 'isOwner', 'technologies', 'workExperiences', 'educations', 'followingCount', 'followersCount'));
    }

    public function show(string $username)
    {
        $user = \App\Models\User::where('username', $username)->with('profile')->firstOrFail();
        $profile = $user->profile;
        $isOwner = Auth::id() === $user->id;

        if ($profile->visibility === 'private' && ! $isOwner) {
            abort(403, 'Este perfil es privado');
        }

        $projects = $user->projects()
            ->with(['media', 'technologies'])
            ->where('privacy', 'public')
            ->withCount('media')
            ->latest()
            ->get();

        if (Auth::check()) {
            $likedIds = ProjectLike::where('user_id', Auth::id())
                ->whereIn('project_id', $projects->pluck('id'))
                ->pluck('project_id')
                ->toArray();
            $projects->each(fn ($p) => $p->isLiked = in_array($p->id, $likedIds));
        }

        $technologies = $user->technologies()->orderBy('name')->get();
        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();
        $followingCount = $user->follows()->count();
        $followersCount = $user->followers()->count();

        return view('profile.index', compact('user', 'profile', 'projects', 'isOwner', 'technologies', 'workExperiences', 'educations', 'followingCount', 'followersCount'));
    }

    public function previewInterno()
    {
        $usuario = Auth::user();
        $datos = $this->prepararDatosCV($usuario);

        return view('components.cv-template', $datos);
    }

    public function downloadCV(?string $username = null)
    {
        if ($username) {
            $usuario = \App\Models\User::where('username', $username)->firstOrFail();
        } else {
            $usuario = Auth::user();
        }

        $datos = $this->prepararDatosCV($usuario);
        $contenido = view('components.cv-template', $datos)->render();

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>* { margin:0; padding:0; box-sizing:border-box; } body { background:#f8fafc; }</style>
</head>
<body>'.$contenido.'</body>
</html>';

        $pdf = Browsershot::html($html)
            ->setNodeBinary('/usr/bin/node')
            ->setNpmBinary('/usr/bin/npm')
            ->setNodeModulePath(env('NODE_MODULES_PATH', '/var/www/html/node_modules'))
            ->setChromePath(env('CHROME_PATH'))
            ->noSandbox()
            ->format('A4')
            ->deviceScaleFactor(1)
            ->margins(0, 0, 0, 0)
            ->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="CV_'.$usuario->username.'.pdf"',
        ]);
    }

    // ══════════════════════════════════════════
    //  PREPARAR DATOS CV (con caché)
    // ══════════════════════════════════════════
    private function prepararDatosCV($usuario): array
    {
        $cvDefaults = [
            'show_photo' => true,
            'show_location' => true,
            'show_email' => true,
            'show_projects' => true,
            'show_experience' => true,
            'show_education' => true,
            'section_order' => ['experience', 'projects', 'education'],
        ];
        $cvSettings = $usuario->profile->cv_settings
            ? array_merge($cvDefaults, $usuario->profile->cv_settings)
            : $cvDefaults;

        return [
            'profile' => $usuario->profile,
            'user' => $usuario,
            'technologies' => $this->cargarIconosTecnologias($usuario),
            'projects' => $usuario->projects()->with('technologies')->latest()->get(),
            'workExperiences' => $usuario->workExperiences()->orderBy('started_at', 'desc')->get(),
            'educations' => $usuario->educations()->orderBy('graduated_year', 'desc')->get(),
            'avatarBase64' => $this->convertirUrlABase64($usuario->profile->avatar ?? null),
            'logoBase64' => 'data:image/png;base64,'.base64_encode(
                file_get_contents(public_path('img/logoFluxa.png'))
            ),
            'qrBase64' => $this->convertirUrlABase64($this->generarUrlQr($usuario->username)),
            'cvSettings' => $cvSettings,
            'urlQrExterno' => $this->generarUrlQr($usuario->username),
        ];
    }

    private function convertirUrlABase64(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        try {
            $contenido = file_get_contents($url);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contenido);

            return "data:{$mime};base64,".base64_encode($contenido);
        } catch (Exception $e) {
            Log::warning('CV: no se pudo convertir imagen a base64', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function generarUrlQr(string $username): string
    {
        $urlPerfil = request()->getHost().'/'.$username;

        return 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
            .urlencode('https://'.$urlPerfil)
            .'&color=0d9488&bgcolor=ffffff&margin=6';
    }

    private function cargarIconosTecnologias($usuario)
    {
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

        return $usuario->technologies()->orderBy('name')->get()
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
    }

    // ══════════════════════════════════════════
    //  AVATAR
    // ══════════════════════════════════════════
    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $avatarUrl = $this->profileService->updateAvatar($user->id, $request->file('avatar'));

            return response()->json(['success' => true, 'url' => $avatarUrl]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyAvatar(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;

            if (! $profile || ! $profile->avatar) {
                return response()->json(['success' => false, 'message' => 'No tienes foto de perfil'], 404);
            }

            $this->profileService->deleteAvatar($user->id);

            Profile::where('user_id', $user->id)->update([
                'avatar' => 'https://api.dicebear.com/7.x/initials/svg?seed='
                    .strtolower($user->username)
                    .'&backgroundColor=12b3b6',
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
