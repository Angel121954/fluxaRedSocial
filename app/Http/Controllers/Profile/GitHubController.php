<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\GitHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function __construct(
        protected GitHubService $gitHubService,
    ) {}

    public function redirectToGitHub()
    {
        $user = Auth::user();

        if ($user->github_token) {
            return redirect()->route('profile.index', ['github_import' => '1']);
        }

        session(['github_import_redirect' => true]);

        return Socialite::driver('github')
            ->scopes(['repo', 'public_repo'])
            ->redirect();
    }

    public function listRepos(): JsonResponse
    {
        $user = Auth::user();

        if (! $user->github_token) {
            return response()->json([
                'success' => false,
                'message' => 'No has conectado tu cuenta de GitHub.',
            ], 401);
        }

        $repos = $this->gitHubService->getRepos($user);

        return response()->json([
            'success' => true,
            'repos' => $repos,
        ]);
    }

    public function importRepo(Request $request): JsonResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'regex:/^[\w.-]+\/[\w.-]+$/'],
        ]);

        $user = Auth::user();

        if (! $user->github_token) {
            return response()->json([
                'success' => false,
                'message' => 'No has conectado tu cuenta de GitHub.',
            ], 401);
        }

        try {
            $result = $this->gitHubService->importRepo($user, $request->full_name);

            $result['project']->load(['user', 'media', 'technologies']);

            $message = 'Proyecto importado.';

            if (! empty($result['skipped_techs'])) {
                $message .= ' Tecnologías omitidas: ' . implode(', ', $result['skipped_techs']) . '.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'project' => $result['project'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error al importar repo de GitHub', [
                'user_id' => $user->id,
                'repo' => $request->full_name,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al importar el repositorio: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function disconnect(): JsonResponse
    {
        $user = Auth::user();

        if (! $user->github_token) {
            return response()->json([
                'success' => false,
                'message' => 'No hay cuenta de GitHub vinculada.',
            ], 404);
        }

        $this->gitHubService->disconnect($user);

        return response()->json([
            'success' => true,
            'message' => 'Cuenta de GitHub desconectada.',
        ]);
    }
}
