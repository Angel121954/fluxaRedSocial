<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\NotificationPreference;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $key = 'register:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Demasiados intentos. Espera {$seconds} segundos.",
            ]);
        }

        RateLimiter::hit($key, 60);

        $user = User::create([
            'name' => strip_tags($request->name),
            'username' => strtolower(strip_tags($request->username)),
            'role' => 'user',
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'avatar' => 'https://api.dicebear.com/7.x/initials/svg?seed='.strtolower($request->username).'&backgroundColor=12b3b6',
        ]);

        NotificationPreference::create([
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('onboarding.technologies');
    }
}
