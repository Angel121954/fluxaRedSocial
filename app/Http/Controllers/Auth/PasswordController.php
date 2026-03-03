<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // ── 1. Validación de formato y reglas ───────────────────────
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        // ── 2. Verificar que la contraseña actual sea correcta ───────
        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('La contraseña actual no es correcta.'),
            ])->errorBag('updatePassword');
        }

        // ── 3. Evitar reutilizar la misma contraseña ─────────────────
        if (Hash::check($validated['password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('La nueva contraseña debe ser diferente a la actual.'),
            ])->errorBag('updatePassword');
        }

        // ── 4. Actualizar la contraseña ──────────────────────────────
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
