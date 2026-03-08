<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function loginAsGuest()
    {
        $uniqueId = uniqid();

        $guest = User::create([
            'name'       => 'Visitante',
            'username'   => 'guest_' . $uniqueId,
            'email'      => 'guest_' . $uniqueId . '@temp.fluxa',
            'password'   => Hash::make(Str::random(32)),
            'role'       => 'guest',
            'status'     => 'temporal',
            'provider'   => 'guest',
            'provider_id' => $uniqueId,
            'onboarding_completed' => '0',
        ]);

        Auth::login($guest);

        session(['is_guest' => true, 'guest_id' => $guest->id]);

        return redirect()->route('explore.index')
            ->with('info', 'Estás explorando como visitante.');
    }

    public function destroyGuest(Request $request)
    {
        $user = Auth::user();

        // Verificar que realmente es un guest
        if (!$user || $user->role !== 'guest') {
            return redirect()->route('login');
        }

        // Guardar el ID antes de cerrar sesión
        $userId = $user->id;

        // Cerrar sesión y limpiar sesión
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Eliminar el registro de la BD
        User::destroy($userId);

        return redirect()->route('login')
            ->with('info', 'Has salido. ¡Vuelve cuando quieras!');
    }
}
