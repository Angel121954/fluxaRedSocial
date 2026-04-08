<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $key = 'register:'.$request->ip();

        // * Para evitar excesivas peticiones al mismo tiempo
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Demasiados intentos. Espera {$seconds} segundos.",
            ]);
        }

        RateLimiter::hit($key, 60);

        $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/',
                function ($attribute, $value, $fail) {
                    $reserved = [
                        'avatar', 'create', 'edit', 'delete', 'update', 'store', 'show',
                        'profile', 'dashboard', 'explore', 'search', 'trending', 'recent',
                        'following', 'topic', 'technologies', 'about-fluxa', 'about',
                        'configuration', 'account', 'security', 'privacy',
                        'notifications', 'notification-preference',
                        'projects', 'work-experiences', 'educations', 'suggestions',
                        'admin', 'onboarding', 'cv', 'download',
                        'guest', 'auth', 'login', 'register', 'logout',
                        'password', 'email', 'verify', 'confirm',
                        'home', 'index', 'api', 'user', 'users',
                    ];
                    if (in_array(strtolower($value), $reserved)) {
                        $fail('Este nombre de usuario no está disponible.');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(6),
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede superar 100 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.max' => 'El nombre de usuario no puede superar 30 caracteres.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números y guiones bajos.',
            'username.not_regex' => 'Este nombre de usuario no está permitido.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no tiene un formato válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

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
