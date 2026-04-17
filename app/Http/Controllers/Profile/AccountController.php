<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        return view('profile.account');
    }

    public function update(UpdateAccountRequest $request)
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $validated = $request->validated();

        if ($validated['email'] !== $user->email) {
            $user->update([
                'email' => $validated['email'],
                'email_verified_at' => null,
            ]);
        }

        $user->profile->update([
            'phone_code' => $validated['phone_code'],
            'phone_number' => $validated['phone_number'],
            'language' => $validated['language'],
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
            'status' => 'pending_deletion',
            'delete_at' => now()->addDays(30), // // se eliminará en 30 días
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
