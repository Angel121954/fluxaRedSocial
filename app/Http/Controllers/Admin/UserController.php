<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrantBadgeRequest;
use App\Models\Badge;
use App\Models\Notification;
use App\Models\User;
use App\Events\UserBanned;
use App\Notifications\CreatesNotifications;
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
            ->where('account_type', 'developer')
            ->latest()
            ->get();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'activo')->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'beta_testers' => Badge::where('slug', 'beta-tester')->first()?->users()->count() ?? 0,
            'banned' => User::where('status', 'banned')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $user->forceFill([
            'status' => 'banned',
            'banned_at' => now(),
            'banned_by' => $request->user()->id,
            'ban_reason' => $request->input('reason'),
        ])->save();

        broadcast(new UserBanned(
            userId: $user->id,
            reason: $request->input('reason') ?? '',
        ));

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario baneado correctamente.');
    }

    public function unban(Request $request, User $user): RedirectResponse
    {
        $user->forceFill([
            'status' => 'activo',
            'banned_at' => null,
            'banned_by' => null,
            'ban_reason' => null,
        ])->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario desbaneado correctamente.');
    }

    public function grantBadge(GrantBadgeRequest $request): RedirectResponse
    {
        $badge = Badge::where('slug', $request->badge_slug)->firstOrFail();

        $pivotData = collect($request->user_ids)->mapWithKeys(
            fn (string $id) => [$id => ['earned_at' => now()]]
        );

        $badge->users()->syncWithoutDetaching($pivotData->toArray());

        $count = count($request->user_ids);

        foreach ($request->user_ids as $userId) {
            CreatesNotifications::createNotification(
                userId: (int) $userId,
                type: Notification::TYPE_BADGE,
                title: '¡Nuevo logro!',
                body: "Fluxa te ha reconocido el logro «{$badge->name}» — {$badge->description}",
                link: route('profile.index', ['tab' => 'badges']),
                fromUserId: null,
                referenceId: $badge->id,
                referenceType: 'badge',
                broadcast: true,
            );
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Insignia otorgada a {$count} usuario(s) correctamente.");
    }
}
