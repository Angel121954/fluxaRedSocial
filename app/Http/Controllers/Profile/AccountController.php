<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateAccountRequest;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $locationService = app(\App\Services\LocationService::class);
        $countries = $locationService->getCountries();
        $userCountry = old('country', Auth()->user()->profile->country ?? null);
        $cities = $userCountry ? $locationService->getCities($userCountry) : [];
        return view('settings.account', compact('countries', 'cities'));
    }

    public function update(UpdateAccountRequest $request)
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $validated = $request->validated();

        if ($validated['email'] !== $user->email) {
            $user->forceFill([
                'email' => $validated['email'],
                'email_verified_at' => null,
            ])->save();
        }

        $profileData = [
            'phone_code' => $validated['phone_code'],
            'phone_number' => $validated['phone_number'],
            'language' => $validated['language'],
            'country' => $validated['country'] ?? null,
            'city' => $validated['city'] ?? null,
        ];

        $countryChanged = $validated['country'] !== $user->profile->country
            || $validated['city'] !== $user->profile->city;

        if (empty($validated['country'])) {
            $profileData['latitude'] = null;
            $profileData['longitude'] = null;
        } elseif ($countryChanged) {
            $locationService = app(\App\Services\LocationService::class);
            $coords = $locationService->geocode(
                $validated['country'],
                $validated['city'] ?? null,
            );

            if ($coords !== null) {
                $profileData['latitude'] = $coords['latitude'];
                $profileData['longitude'] = $coords['longitude'];
            } else {
                return redirect()->route('account.index')
                    ->with('error', 'No se pudo determinar la ubicación en el mapa para el país/ciudad seleccionado. Los datos se guardaron sin coordenadas.')
                    ->withInput();
            }
        } else {
            $profileData['latitude'] = $user->profile->latitude;
            $profileData['longitude'] = $user->profile->longitude;
        }

        $user->profile->update($profileData);

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
        $user->forceFill([
            'status' => 'pending_deletion',
            'delete_at' => now()->addDays(30),
        ])->save();

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
