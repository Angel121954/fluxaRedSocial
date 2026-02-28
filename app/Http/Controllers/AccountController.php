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
        $user = Auth::user()->id;

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone_code' => ['required', 'string', 'max:5'],
            'phone_number' => ['required', 'string', 'max:15'],
            'language' => ['required', 'string'],
        ], [
            'email.required' => 'El email es obligatorio',
            'email.max' => 'El email debe tener un máximo de 255 caracteres',
            'phone_code.required' => 'El código de marcación internacional es obligatorio',
            'phone_code.max' => 'El código no puede superar los 5 caracteres',
            'phone_number.required' => 'El número de télefono es obligatorio',
            'phone_number.max' => 'El número de télefono no puede superar los 15 caracteres',
            'language.required' => 'El lenguaje es obligatorio',
        ]);

        if ($validated['email'] != Auth::user()->email) {
            User::where('id', $user)->update([
                'email' => $validated['email'],
                'email_verified_at' => null,
            ]);
        }

        Profile::where('user_id', $user)->update([
            'phone_code'   => $validated['phone_code'],
            'phone_number' => $validated['phone_number'],
            'language'     => $validated['language'],
        ]);

        return redirect()->route('account.index')
            ->with('success', 'Cambios guardados correctamente.');
    }

    public function deletePhoto()
    {
        $user = Auth::user();

        return redirect()->route('account.index')
            ->with('success', 'Foto de perfil eliminada.');
    }
}
