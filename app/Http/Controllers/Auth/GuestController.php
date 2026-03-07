<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'provider_id'=> $uniqueId,
            'onboarding_completed' => '0',
        ]);

        Auth::login($guest);

        // Marcamos en sesión que es visitante
        session(['is_guest' => true, 'guest_id' => $guest->id]);

        return redirect()->route('explore.index')
            ->with('info', 'Estás explorando como visitante.');
    }
}