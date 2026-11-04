<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BlockService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function __construct(
        protected BlockService $blockService,
    ) {}

    public function store(User $user): JsonResponse
    {
        $currentUser = request()->user();

        if ($currentUser->id === $user->id) {
            return response()->json(['message' => 'No puedes bloquearte a ti mismo.'], 422);
        }

        $this->blockService->block($currentUser, $user);

        return response()->json([
            'blocked' => true,
            'message' => 'Usuario bloqueado.',
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->blockService->unblock(request()->user(), $user);

        return response()->json([
            'blocked' => false,
            'message' => 'Usuario desbloqueado.',
        ]);
    }

    public function index(): View
    {
        $user = request()->user()->load('profile');

        return view('settings.blocked', [
            'profile' => $user->profile,
            'blockedUsers' => $this->blockService->blockedUsers($user),
        ]);
    }
}
