<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Education;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    private const MAX_EDUCATIONS = 5;

    public function index()
    {
        /** @var \App\Models\User $user */
        $user     = Auth::user();
        $profile  = $user->profile;

        $educations = Education::where('user_id', $user->id)
            ->orderByDesc('graduated_year')
            ->get();

        return view('profile.educations', [
            'profile'    => $profile,
            'educations' => $educations,
            'limits'     => ['max_educations' => self::MAX_EDUCATIONS],
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (Education::where('user_id', $user->id)->count() >= self::MAX_EDUCATIONS) {
            return back()->with('error', 'Has alcanzado el límite máximo de educaciones.');
        }

        $validated = $request->validate([
            'institution'    => ['required', 'string', 'max:150'],
            'degree'         => ['required', 'string', 'max:150'],
            'field'          => ['nullable', 'string', 'max:150'],
            'graduated_year' => ['nullable', 'integer', 'min:1950', 'max:' . (date('Y') + 10)],
            'current'        => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = $user->id;
        $validated['current'] = $request->boolean('current');

        // Si está en curso, limpiar el año de graduación
        if ($validated['current']) {
            $validated['graduated_year'] = null;
        }

        Education::create($validated);

        return back()->with('success', 'Educación agregada correctamente.');
    }

    public function update(Request $request, Education $education)
    {
        $validated = $request->validate([
            'institution'    => ['required', 'string', 'max:150'],
            'degree'         => ['required', 'string', 'max:150'],
            'field'          => ['nullable', 'string', 'max:150'],
            'graduated_year' => ['nullable', 'integer', 'min:1950', 'max:' . (date('Y') + 10)],
            'current'        => ['nullable', 'boolean'],
        ]);

        $validated['current'] = $request->boolean('current');

        if ($validated['current']) {
            $validated['graduated_year'] = null;
        }

        $education->update($validated);

        return back()->with('success', 'Educación actualizada correctamente.');
    }

    public function destroy(Education $education)
    {
        $education->delete();
        return back()->with('success', 'Educación eliminada correctamente.');
    }
}
