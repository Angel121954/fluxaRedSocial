<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdateTechnologiesRequest;
use App\Models\Conversation;
use App\Models\Profile;
use App\Models\Technology;
use App\Models\User;
use App\Services\BadgeService;
use App\Services\CVService;
use App\Services\ProfileService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService,
        protected CVService $cvService,
        protected BadgeService $badgeService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $user->loadCount(['followers', 'follows'])->load('profile');
        $profile = $user->profile;

        $data = $this->profileService->loadProfileData($user, $user, true);

        return view('profile.index', [
            'user' => $user,
            'profile' => $profile,
            'isOwner' => true,
            ...$data,
        ]);
    }

    public function show(string $username)
    {
        $user = User::where('username', $username)
            ->select('id', 'username', 'email', 'name', 'role')
            ->with('profile')
            ->firstOrFail();
        $profile = $user->profile;

        $this->authorize('view', $profile);

        $user->loadCount(['followers', 'follows']);
        $isOwner = Auth::id() === $user->id;
        $isFollowing = Auth::check() && Auth::user()->follows()->where('followed_id', $user->id)->exists();
        $isFollowedBy = Auth::check() && $user->follows()->where('followed_id', Auth::id())->exists();

        $showFavorites = $isOwner || $profile->show_favorites;
        $data = $this->profileService->loadProfileData($user, Auth::user(), $showFavorites);

        $conversation = null;
        if (Auth::check() && ! $isOwner) {
            $conversation = Conversation::where(function ($q) use ($user) {
                $q->where('user_a_id', auth()->id())->where('user_b_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('user_a_id', $user->id)->where('user_b_id', auth()->id());
            })->first();
        }

        return view('profile.index', [
            'user' => $user,
            'profile' => $profile,
            'isOwner' => $isOwner,
            'isFollowing' => $isFollowing,
            'isFollowedBy' => $isFollowedBy,
            'conversation' => $conversation,
            ...$data,
        ]);
    }

    public function projects(User $user): JsonResponse
    {
        $isOwner = Auth::id() === $user->id;

        $query = $user->projects()
            ->select('id', 'user_id', 'title', 'content', 'privacy', 'created_at', 'updated_at')
            ->withCount(['media', 'likes', 'comments']);

        if (! $isOwner) {
            $query->where('privacy', 'public');
        }

        $projects = $query->latest()->get()->map(fn ($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'content' => Str::limit($p->content, 120),
            'media_count' => $p->media_count,
            'likes_count' => $p->likes_count,
            'comments_count' => $p->comments_count,
            'created_at' => $p->created_at->diffForHumans(),
        ]);

        return response()->json(['projects' => $projects]);
    }

    public function previewInterno()
    {
        $usuario = Auth::user();
        $datos = $this->cvService->prepareCvData($usuario);

        return view('components.cv-template', $datos);
    }

    public function downloadCV(?string $username = null)
    {
        if ($username) {
            $usuario = User::where('username', $username)->firstOrFail();
            $profile = $usuario->profile;

            $isOwner = Auth::id() === $usuario->id;
            $this->authorize('view', $profile);
        } else {
            $usuario = Auth::user();
        }

        $datos = $this->cvService->prepareCvData($usuario);
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

        $pdf = $this->cvService->generatePdf($html);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="CV_'.$usuario->username.'.pdf"',
        ]);
    }

    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $avatarUrl = $this->profileService->updateAvatar($user->id, $request->file('avatar'));

            $this->badgeService->scanUser($user);

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

    public function updateTechnologies(UpdateTechnologiesRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->technologies()->sync($request->technologies ?? []);

        $this->badgeService->scanUser($user);

        return response()->json(['success' => true]);
    }

    public function toggleFavoriteTechnology(Technology $technology): JsonResponse
    {
        $user = request()->user();

        $alreadyFav = $user->technologies()
            ->wherePivot('is_favorite', true)
            ->where('technology_id', '!=', $technology->id)
            ->count();

        $isCurrentlyFav = $user->technologies()
            ->wherePivot('is_favorite', true)
            ->where('technology_id', $technology->id)
            ->exists();

        if (! $isCurrentlyFav && $alreadyFav >= 3 && $user->role != 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes tener hasta 3 tecnologías destacadas como desarrollador',
            ], 422);
        }

        $isFavorite = $user->toggleFavoriteTechnology((int) $technology->id);

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
        ]);
    }
}
