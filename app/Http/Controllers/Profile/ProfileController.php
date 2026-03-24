<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Models\Profile;
use Exception;

class ProfileController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        /** @var \App\Models\User $user */
        $profile = $user->profile;

        $projects = $user->projects()
            ->with(['media', 'technologies'])
            ->withCount('media')
            ->latest()
            ->get();

        $technologies    = $user->technologies()->orderBy('name')->get();
        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();

        // Precalentar caché del CV en background al cargar el perfil
        dispatch(function () use ($user) {
            $this->prepararDatosCV($user);
        })->afterResponse();

        return view('profile.profile', compact('profile', 'projects', 'technologies', 'workExperiences', 'educations'));
    }

    public function previewInterno()
    {
        $usuario = Auth::user();
        $datos   = $this->prepararDatosCV($usuario);
        return view('components.cv-template', $datos);
    }

    public function descargarCV()
    {
        $usuario   = Auth::user();
        $datos     = $this->prepararDatosCV($usuario);
        $contenido = view('components.cv-template', $datos)->render();

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>* { margin:0; padding:0; box-sizing:border-box; } body { background:#f8fafc; }</style>
</head>
<body>' . $contenido . '</body>
</html>';

        $pdf = Browsershot::html($html)
            ->setNodeBinary(env('NODE_BINARY', '/usr/bin/node'))
            ->setNpmBinary(env('NPM_BINARY', '/usr/bin/npm'))
            ->setChromePath(env('CHROME_PATH', '/usr/bin/google-chrome'))
            ->noSandbox()
            ->format('A4')
            ->deviceScaleFactor(1)
            ->margins(0, 0, 0, 0)
            ->pdf();

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="CV_' . $usuario->username . '.pdf"',
        ]);
    }

    // ══════════════════════════════════════════
    //  PREPARAR DATOS CV (con caché)
    // ══════════════════════════════════════════
    private function prepararDatosCV($usuario): array
    {
        $cacheKey = "cv_imagenes_{$usuario->id}";

        $imagenesCacheadas = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($usuario) {
            return [
                'avatarBase64' => $this->convertirUrlABase64($usuario->profile->avatar ?? null),
                'logoBase64'   => 'data:image/png;base64,' . base64_encode(
                    file_get_contents(public_path('img/logoFluxa.png'))
                ),
                'qrBase64'     => $this->convertirUrlABase64($this->generarUrlQr($usuario->username)),
                'technologies' => $this->cargarIconosTecnologias($usuario),
            ];
        });

        return [
            'profile'         => $usuario->profile,
            'technologies'    => $imagenesCacheadas['technologies'],
            'projects'        => $usuario->projects()->with('technologies')->latest()->get(),
            'workExperiences' => $usuario->workExperiences()->orderBy('started_at', 'desc')->get(),
            'educations'      => $usuario->educations()->orderBy('graduated_year', 'desc')->get(), // 👈 FALTABA
            'avatarBase64'    => $imagenesCacheadas['avatarBase64'],
            'logoBase64'      => $imagenesCacheadas['logoBase64'],
            'qrBase64'        => $imagenesCacheadas['qrBase64'],
        ];
    }
    private function convertirUrlABase64(?string $url): ?string
    {
        if (empty($url)) return null;
        try {
            $contenido = file_get_contents($url);
            $mime      = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contenido);
            return "data:{$mime};base64," . base64_encode($contenido);
        } catch (Exception $e) {
            Log::warning('CV: no se pudo convertir imagen a base64', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function generarUrlQr(string $username): string
    {
        $urlPerfil = request()->getHost() . '/' . $username;
        return 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
            . urlencode('https://' . $urlPerfil)
            . '&color=0d9488&bgcolor=ffffff&margin=6';
    }

    private function cargarIconosTecnologias($usuario)
    {
        $excepcionesIcono = [
            'amazonwebservices' => 'plain-wordmark',
            'angularjs'         => 'plain',
            'django'            => 'plain',
            'tailwindcss'       => 'plain',
            'kubernetes'        => 'plain',
            'graphql'           => 'plain',
            'firebase'          => 'plain',
            'express'           => 'original-wordmark',
        ];

        return $usuario->technologies()->orderBy('name')->get()
            ->map(function ($tech) use ($excepcionesIcono) {
                $slug = (string) $tech->slug;
                $tipo = $excepcionesIcono[$slug] ?? 'original';
                $url  = "https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{$slug}/{$slug}-{$tipo}.svg";
                try {
                    $tech->iconoB64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($url));
                } catch (Exception $e) {
                    $tech->iconoB64 = null;
                }
                return $tech;
            });
    }

    // ══════════════════════════════════════════
    //  INVALIDAR CACHÉ (llamar cuando cambie el perfil)
    // ══════════════════════════════════════════
    public static function invalidarCacheCV(int $userId): void
    {
        Cache::forget("cv_imagenes_{$userId}");
    }

    // ══════════════════════════════════════════
    //  AVATAR
    // ══════════════════════════════════════════
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ], [
            'avatar.required' => 'La imagen es obligatoria para poder actualizar',
            'avatar.image'    => 'Debe ser una imagen válida',
            'avatar.max'      => 'La imagen no puede superar los 2 MB',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user      = Auth::user();
            $avatarUrl = $request->file('avatar')->getRealPath();

            $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
            $result     = $cloudinary->uploadApi()->upload($avatarUrl, [
                'folder'         => 'avatares',
                'public_id'      => 'user_' . $user->id,
                'overwrite'      => true,
                'transformation' => [[
                    'width'        => 400,
                    'height'       => 400,
                    'crop'         => 'fill',
                    'gravity'      => 'face',
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                ]],
            ]);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['avatar'  => $result['secure_url']]
            );

            // Invalidar caché del CV para que tome el nuevo avatar
            self::invalidarCacheCV($user->id);

            return response()->json(['success' => true, 'url' => $result['secure_url']]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyAvatar(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user    = Auth::user();
            $profile = $user->profile;

            if (!$profile || !$profile->avatar) {
                return response()->json(['success' => false, 'message' => 'No tienes foto de perfil'], 404);
            }

            $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
            $cloudinary->uploadApi()->destroy('avatares/user_' . $user->id);
            $profile->update(['avatar' => null]);

            // Invalidar caché del CV
            self::invalidarCacheCV($user->id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
