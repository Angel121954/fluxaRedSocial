<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateTechnologiesRequest;
use App\Models\Badge;
use App\Models\Conversation;
use App\Models\Profile;
use App\Models\Technology;
use App\Models\User;
use App\Services\BadgeService;
use App\Services\CVService;
use App\Services\ProfileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService,
        protected CVService $cvService,
        protected BadgeService $badgeService,
    ) {}

    public function index()
    {
        $user = Auth::user();
        $user->loadCount(['followers', 'follows']);
        $user->load('profile');
        $profile = $user->profile;
        $isOwner = true;

        $projects = $user->projects()
            ->with([
                'user',
                'media',
                'technologies',
                'likes' => fn ($q) => $q->where('user_id', $user->id),
                'bookmarks' => fn ($q) => $q->where('user_id', $user->id),
                'skillEndorsements',
            ])
            ->withCount(['media', 'likes', 'comments'])
            ->latest()
            ->get();

        $technologies = $user->technologies()->orderBy('name')->get();
        $allTechnologies = Technology::orderBy('name')->get();
        $userTechnologies = $technologies;
        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();

        $favoriteProjects = $user->bookmarkedProjects()
            ->latest()
            ->get();

        $projectsById = $projects->keyBy('id');

        $favoriteProjects->each(function ($project) use ($projectsById) {
            if ($existing = $projectsById->get($project->id)) {
                $project->setRelation('user', $existing->user);
                $project->setRelation('media', $existing->media);
                $project->setRelation('technologies', $existing->technologies);
            }
        });

        $needsLoad = $favoriteProjects->reject(fn ($p) => $projectsById->has($p->id));

        if ($needsLoad->isNotEmpty()) {
            $needsLoad->load(['user', 'media', 'technologies']);
        }

        $badges = $user->badges()->get();
        $allBadges = Badge::orderBy('order')->get();

        return view('profile.index', compact(
            'user',
            'profile',
            'projects',
            'isOwner',
            'technologies',
            'allTechnologies',
            'userTechnologies',
            'workExperiences',
            'educations',
            'favoriteProjects',
            'badges',
            'allBadges',
        ));
    }

    public function show(string $username)
    {
        $conversation = null;
        $user = User::where('username', $username)->with('profile')->firstOrFail();
        $profile = $user->profile;

        $this->authorize('view', $profile);

        $user->loadCount(['followers', 'follows']);
        $isOwner = Auth::id() === $user->id;
        $isFollowing = Auth::check() && Auth::user()->follows()->where('followed_id', $user->id)->exists();
        $isFollowedBy = Auth::check() && $user->follows()->where('followed_id', Auth::id())->exists();

        $projects = $user->projects()
            ->with([
                'user',
                'media',
                'technologies',
                'likes' => fn ($q) => $q->where('user_id', auth()->id()),
                'bookmarks' => fn ($q) => $q->where('user_id', auth()->id()),
                'skillEndorsements',
            ])
            ->withCount(['media', 'likes', 'comments'])
            ->where('privacy', 'public')
            ->latest()
            ->get();

        if (Auth::check()) {
            if (! $isOwner) {
                $conversation = Conversation::where(function ($q) use ($user) {
                    $q->where('user_a_id', auth()->id())->where('user_b_id', $user->id);
                })->orWhere(function ($q) use ($user) {
                    $q->where('user_a_id', $user->id)->where('user_b_id', auth()->id());
                })->first();
            }
        }

        $technologies = $user->technologies()->orderBy('name')->get();
        $allTechnologies = Technology::orderBy('name')->get();
        $userTechnologies = $technologies;
        $workExperiences = $user->workExperiences()->orderBy('started_at', 'desc')->get();
        $educations = $user->educations()->orderBy('graduated_year', 'desc')->get();

        $favoriteProjects = collect();
        if ($isOwner || $profile->show_favorites) {
            $favoriteProjects = $user->bookmarkedProjects()
                ->latest()
                ->get();

            $projectsById = $projects->keyBy('id');

            $favoriteProjects->each(function ($project) use ($projectsById) {
                if ($existing = $projectsById->get($project->id)) {
                    $project->setRelation('user', $existing->user);
                    $project->setRelation('media', $existing->media);
                    $project->setRelation('technologies', $existing->technologies);
                }
            });

            $needsLoad = $favoriteProjects->reject(fn ($p) => $projectsById->has($p->id));

            if ($needsLoad->isNotEmpty()) {
                $needsLoad->load(['user', 'media', 'technologies']);
            }
        }

        $badges = $user->badges()->get();
        $allBadges = Badge::orderBy('order')->get();

        return view('profile.index', compact(
            'user',
            'profile',
            'projects',
            'isOwner',
            'isFollowing',
            'isFollowedBy',
            'technologies',
            'allTechnologies',
            'userTechnologies',
            'workExperiences',
            'educations',
            'favoriteProjects',
            'badges',
            'allBadges',
            'conversation'
        ));
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
}
