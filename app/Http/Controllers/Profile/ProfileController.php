<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Models\Conversation;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
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
        $user = Auth::user();
        $user->load(['profile', 'followers', 'follows', 'technologies', 'workExperiences', 'educations']);
        $user->loadCount(['followers', 'follows']);
        $profile = $user->profile;
        $isOwner = true;

        $projects = $user->projects()
            ->with([
                'user.profile',
                'media',
                'technologies',
                'likes' => fn ($q) => $q->where('user_id', $user->id),
                'bookmarks' => fn ($q) => $q->where('user_id', $user->id),
                'skillEndorsements',
            ])
            ->withCount(['media', 'likes'])
            ->latest()
            ->get();

        $projectsCount = $projects->count();

        $projects->each(function ($project) use ($user) {
            $project->precomputed_is_liked = $project->likes->isNotEmpty();
            $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
            $project->precomputed_user_endorsement = $project->skillEndorsements
                ->where('user_id', $user->id)
                ->first()?->skill_type;
        });

        $bookmarkedProjects = $user->projectBookmarks()
            ->with([
                'user.profile',
                'media',
                'technologies',
                'likes' => fn ($q) => $q->where('user_id', $user->id),
                'bookmarks' => fn ($q) => $q->where('user_id', $user->id),
                'skillEndorsements',
            ])
            ->withCount(['media', 'likes'])
            ->latest()
            ->get();

        $bookmarkedProjects->each(function ($project) use ($user) {
            $project->precomputed_is_liked = $project->likes->isNotEmpty();
            $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
            $project->precomputed_user_endorsement = $project->skillEndorsements
                ->where('user_id', $user->id)
                ->first()?->skill_type;
        });

        $technologies = $user->technologies;
        $workExperiences = $user->workExperiences;
        $educations = $user->educations;

        return view('profile.index', compact(
            'user',
            'profile',
            'projects',
            'projectsCount',
            'isOwner',
            'technologies',
            'workExperiences',
            'educations',
            'bookmarkedProjects',
        ));
    }

    public function show(string $username)
    {
        $user = User::where('username', $username)
            ->with(['profile', 'followers', 'follows', 'technologies', 'workExperiences', 'educations'])
            ->firstOrFail();
        $user->loadCount(['followers', 'follows']);
        $profile = $user->profile;
        $isOwner = Auth::id() === $user->id;
        $projectsCount = null;
        $bookmarkedProjects = null;

        $projects = $user->projects()
            ->with([
                'user.profile',
                'media',
                'technologies',
                'likes' => fn ($q) => $q->where('user_id', auth()->id()),
                'bookmarks' => fn ($q) => $q->where('user_id', auth()->id()),
                'skillEndorsements',
            ])
            ->withCount(['media', 'likes'])
            ->where('privacy', 'public')
            ->latest()
            ->get();

        $projectsCount = $projects->count();

        $projects->each(function ($project) use ($user) {
            $project->precomputed_is_liked = $project->likes->isNotEmpty();
            $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
            $project->precomputed_user_endorsement = $project->skillEndorsements
                ->where('user_id', $user->id)
                ->first()?->skill_type;
        });

        if ($isOwner) {
            $bookmarkedProjects = $user->projectBookmarks()
                ->with([
                    'user.profile',
                    'media',
                    'technologies',
                    'likes' => fn ($q) => $q->where('user_id', $user->id),
                    'bookmarks' => fn ($q) => $q->where('user_id', $user->id),
                    'skillEndorsements',
                ])
                ->withCount(['media', 'likes'])
                ->latest()
                ->get();

            $bookmarkedProjects->each(function ($project) use ($user) {
                $project->precomputed_is_liked = $project->likes->isNotEmpty();
                $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
                $project->precomputed_user_endorsement = $project->skillEndorsements
                    ->where('user_id', $user->id)
                    ->first()?->skill_type;
            });
        } elseif ($profile && $profile->show_bookmarks) {
            $bookmarkedProjects = $user->projectBookmarks()
                ->with([
                    'user.profile',
                    'media',
                    'technologies',
                    'likes' => fn ($q) => $q->where('user_id', auth()->id()),
                    'bookmarks' => fn ($q) => $q->where('user_id', auth()->id()),
                    'skillEndorsements',
                ])
                ->withCount(['media', 'likes'])
                ->latest()
                ->get();

            $bookmarkedProjects->each(function ($project) use ($user) {
                $project->precomputed_is_liked = $project->likes->isNotEmpty();
                $project->precomputed_is_bookmarked = $project->bookmarks->isNotEmpty();
                $project->precomputed_user_endorsement = $project->skillEndorsements
                    ->where('user_id', $user->id)
                    ->first()?->skill_type;
            });
        }

        $conversation = null;
        if (Auth::check() && !$isOwner) {
            $conversation = Conversation::where(function ($q) use ($user) {
                $q->where('user_a_id', auth()->id())->where('user_b_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->where('user_b_id', auth()->id());
            })->first();
        }

        $technologies = $user->technologies;
        $workExperiences = $user->workExperiences;
        $educations = $user->educations;

        return view('profile.index', compact(
            'user',
            'profile',
            'projects',
            'projectsCount',
            'isOwner',
            'technologies',
            'workExperiences',
            'educations',
            'bookmarkedProjects',
            'conversation'
        ));
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
            $usuario = User::where('username', $username)->firstOrFail();

            $isOwner = Auth::id() === $usuario->id;
            if ($usuario->profile->visibility === 'private' && ! $isOwner) {
                abort(403, 'Este perfil es privado');
            }
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
            ->timeout(300)
            ->setOption('waitUntil', 'domcontentloaded')
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="CV_'.$usuario->username.'.pdf"',
        ]);
    }

    // ══════════════════════════════════════════
    //  PREPARAR DATOS CV
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
            'projects' => $usuario->projects()
                ->with([
                    'media',
                    'technologies',
                    'likes',
                    'bookmarks',
                    'skillEndorsements',
                ])
                ->latest()
                ->get(),
            'workExperiences' => $usuario->workExperiences()->orderBy('started_at', 'desc')->get(),
            'educations' => $usuario->educations()->orderBy('graduated_year', 'desc')->get(),
            'avatarBase64' => $this->convertirUrlABase64($usuario->profile->avatar ?? null),
            'logoBase64' => 'data:image/png;base64,'.base64_encode(
                file_get_contents(public_path('img/logoFluxa.png'))
            ),
            'qrBase64' => $this->generarQrCode('https://'.request()->getHost().'/'.$usuario->username),
            'cvSettings' => $cvSettings,
            'urlQrExterno' => null,
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

    private function generarQrCode(string $data): ?string
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
