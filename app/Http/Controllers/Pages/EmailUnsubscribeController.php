<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class EmailUnsubscribeController extends Controller
{
    public function show(User $user): View
    {
        return view('public.unsubscribe', [
            'profile' => null,
            'isSubscribed' => $user->notificationPreferences?->email_enabled ?? true,
            'unsubscribeActionUrl' => URL::signedRoute(
                'email.unsubscribe.destroy',
                ['user' => $user->id],
            ),
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse|Response
    {
        $user->notificationPreferences()->updateOrCreate([], [
            'email_enabled' => false,
            'weekly_summary' => false,
        ]);

        AnalyticsService::track('email_unsubscribed', $user, [], $request);

        if ($request->hasHeader('List-Unsubscribe-Post')) {
            return response()->noContent();
        }

        return redirect()->route('landing')
            ->with('success', 'Listo. No volverás a recibir el resumen semanal de Fluxa.');
    }
}
