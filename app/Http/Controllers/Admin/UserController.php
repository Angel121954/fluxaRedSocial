<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('badges', 'profile')
            ->withCount('projects')
            ->where('role', '!=', 'admin')
            ->latest()
            ->get();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'activo')->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'beta_testers' => Badge::where('slug', 'beta_tester')->first()?->users()->count() ?? 0,
            'banned' => User::where('status', 'banned')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => 'banned']);

        return redirect()->route('admin.users.index')
            ->with('status', 'Usuario baneado correctamente.');
    }

    public function unban(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => 'activo']);

        return redirect()->route('admin.users.index')
            ->with('status', 'Usuario desbaneado correctamente.');
    }

    public function grantBadge(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
        ]);

        $badge = Badge::where('slug', 'beta_tester')->first();

        if (! $badge) {
            return redirect()->route('admin.users.index')
                ->with('error', 'La insignia Beta Tester no existe.');
        }

        $badge->users()->syncWithoutDetaching($request->user_ids);

        $count = count($request->user_ids);

        return redirect()->route('admin.users.index')
            ->with('status', "Insignia otorgada a {$count} usuario(s) correctamente.");
    }
}
