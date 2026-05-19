<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ProblemReport;
use App\Models\ProjectReport;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'activo')->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();

        $suggestionsCount = Suggestion::count();
        $suggestionsByStatus = Suggestion::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalReports = ProblemReport::count() + UserReport::count() + ProjectReport::count();

        $userGrowth = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topUsers = User::withCount('projects')
            ->with('profile')
            ->orderBy('projects_count', 'desc')
            ->take(5)
            ->get();

        $recentUsers = User::select('id', 'name', 'username', 'created_at')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($u) => [
                'type' => 'user',
                'title' => 'Nuevo usuario registrado',
                'description' => "{$u->name} se unió a Fluxa",
                'time' => $u->created_at,
            ]);

        $recentSuggestions = Suggestion::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($s) => [
                'type' => 'suggestion',
                'title' => 'Nueva sugerencia',
                'description' => $s->description,
                'time' => $s->created_at,
            ]);

        $recentContacts = Contact::where('readed', false)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($c) => [
                'type' => 'contact',
                'title' => 'Nuevo mensaje de contacto',
                'description' => "{$c->name}: {$c->message}",
                'time' => $c->created_at,
            ]);

        $activity = collect()
            ->merge($recentUsers)
            ->merge($recentSuggestions)
            ->merge($recentContacts)
            ->sortByDesc('time')
            ->take(5)
            ->values();

        return view('admin.index', compact(
            'totalUsers',
            'activeUsers',
            'verifiedUsers',
            'suggestionsCount',
            'suggestionsByStatus',
            'totalReports',
            'userGrowth',
            'topUsers',
            'activity',
        ));
    }
}
