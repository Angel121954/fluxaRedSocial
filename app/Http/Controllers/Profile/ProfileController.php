<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Profile;
use Exception;

class ProfileController extends Controller
{
    /**
     * Muestra el perfil del usuario autenticado.
     */
    public function index(): View
    {
        $profile = Auth::user()->profile;

        return view('profile.profile', compact('profile'));
    }

    /**
     * Sube y actualiza el avatar del usuario en Cloudinary.
     */
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
                'transformation' => [
                    [
                        'width'        => 400,
                        'height'       => 400,
                        'crop'         => 'fill',
                        'gravity'      => 'face',
                        'quality'      => 'auto',
                        'fetch_format' => 'auto',
                    ],
                ],
            ]);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['avatar'  => $result['secure_url']]
            );

            return response()->json([
                'success' => true,
                'url'     => $result['secure_url'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina el avatar del usuario de Cloudinary y la BD.
     */
    public function destroyAvatar(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user    = Auth::user();
            $profile = $user->profile;

            if (!$profile || !$profile->avatar) {
                return response()->json(['success' => false, 'message' => 'No tienes foto de perfil'], 404);
            }

            // 1. Eliminar de Cloudinary usando el public_id
            $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
            $cloudinary->uploadApi()->destroy('avatares/user_' . $user->id);

            // 2. Eliminar la URL de la BD
            $profile->update(['avatar' => null]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
