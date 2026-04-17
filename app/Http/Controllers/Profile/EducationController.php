<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationRequest;
use App\Http\Requests\UpdateEducationRequest;
use App\Models\Education;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    private const MAX_EDUCATIONS = 5;

    public function index()
    {
        $educations = Education::where('user_id', Auth::id())
            ->orderByDesc('graduated_year')
            ->get();

        return view('profile.educations', [
            'educations' => $educations,
            'limits' => ['max_educations' => self::MAX_EDUCATIONS],
        ]);
    }

    public function store(StoreEducationRequest $request)
    {
        if (Education::where('user_id', Auth::id())->count() >= self::MAX_EDUCATIONS) {
            return back()->with('error', 'Has alcanzado el límite máximo de educaciones.');
        }

        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['current'] = $request->boolean('current');

        if ($validated['current']) {
            $validated['graduated_year'] = null;
        }

        Education::create($validated);

        return back()->with('success', 'Educación agregada correctamente.');
    }

    public function update(UpdateEducationRequest $request, Education $education)
    {
        $validated = $request->validated();
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
