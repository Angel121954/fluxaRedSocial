<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Events\UserBanned;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = User::with('profile')
            ->withCount('projects')
            ->where('account_type', 'company')
            ->latest()
            ->get();

        return view('admin.companies.index', compact('companies'));
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'status' => 'banned',
            'banned_at' => now(),
            'banned_by' => $request->user()->id,
            'ban_reason' => $request->input('reason'),
        ]);

        broadcast(new UserBanned(
            userId: $user->id,
            reason: $request->input('reason') ?? '',
        ));

        return redirect()->route('admin.companies.index')
            ->with('success', 'Empresa baneada correctamente.');
    }

    public function unban(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'status' => 'activo',
            'banned_at' => null,
            'banned_by' => null,
            'ban_reason' => null,
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Empresa desbaneada correctamente.');
    }
}
