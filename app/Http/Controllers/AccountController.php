<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Profile;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'avatar'       => null,
                'phone_code'   => null,
                'phone_number' => null,
                'language'     => 'es',
            ]
        );

        return view('profile.account', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'phone_code'   => ['required', 'string', 'max:5'],
            'phone_number' => ['required', 'string', 'max:15'],
            'language'     => ['required', 'string'],
        ], [
            'email.required'      => 'El email es obligatorio',
            'email.unique'        => 'Este email ya está registrado.',
            'phone_code.required' => 'El código de marcación internacional es obligatorio',
        ]);

        if ($validated['email'] !== $user->email) {
            $user->update([
                'email'             => $validated['email'],
                'email_verified_at' => null,
            ]);
        }

        $user->profile->update([
            'phone_code'   => $validated['phone_code'],
            'phone_number' => $validated['phone_number'],
            'language'     => $validated['language'],
        ]);

        return redirect()->route('account.index')
            ->with('success', 'Cambios guardados correctamente.');
    }

    public function deactivate()
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */

        $user->update(['status' => 'inactivo']);

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Cuenta desactivada. Inicia sesión para reactivarla.');
    }

    public function destroy()
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */

        $user->update([
            'status'    => 'pending_deletion',
            'delete_at' => now()->addDays(30), //// se eliminará en 30 días
        ]);

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Tu cuenta será eliminada en 30 días. Puedes detener está acción iniciando sesión.');
    }

    public function deletePhoto()
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */

        return redirect()->route('account.index')
            ->with('success', 'Foto de perfil eliminada.');
    }
}
