<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkExperienceController extends Controller
{
    // ── Límites por plan ─────────────────────────────────────────────
    private function getLimits(): array
    {
        // TODO: cuando implementes planes, descomenta esto y elimina el return de abajo
        // $plan = Auth::user()->plan ?? 'free';
        // return config("plans.{$plan}");

        return [
            'max_work_experiences' => 5,
            'max_current_jobs'     => 2,
        ];
    }

    public function index()
    {
        $experiences = WorkExperience::where('user_id', Auth::id())
            ->orderBy('started_at', 'desc')
            ->get();

        $profile = Auth::user()->profile;
        $limits  = $this->getLimits();

        return view('profile.work-experiences', compact('experiences', 'profile', 'limits'));
    }

    public function store(Request $request)
    {
        $limits     = $this->getLimits();
        $totalCount = WorkExperience::where('user_id', Auth::id())->count();

        if ($totalCount >= $limits['max_work_experiences']) {
            return back()
                ->withInput()
                ->withErrors(['company' => "Has alcanzado el límite de {$limits['max_work_experiences']} experiencias laborales."]);
        }

        $validated = $request->validate([
            'company'     => 'required|string|max:100',
            'position'    => 'required|string|max:100',
            'location'    => 'nullable|string|max:100',
            'started_at'  => 'required|date',
            'ended_at'    => 'nullable|date|after_or_equal:started_at|required_if:current,0',
            'current'     => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['current'] = $request->boolean('current');

        if ($validated['current']) {
            $validated['ended_at'] = null;
        }

        WorkExperience::create($validated);

        return redirect()->route('work-experiences.index')
            ->with('success', 'Experiencia laboral agregada correctamente.');
    }

    public function update(Request $request, WorkExperience $workExperience)
    {
        if ($workExperience->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'company'     => 'required|string|max:100',
            'position'    => 'required|string|max:100',
            'location'    => 'nullable|string|max:100',
            'started_at'  => 'required|date',
            'ended_at'    => 'nullable|date|after_or_equal:started_at|required_if:current,0',
            'current'     => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['current'] = $request->boolean('current');

        if ($validated['current']) {
            $validated['ended_at'] = null;
        }

        $workExperience->update($validated);

        return redirect()->route('work-experiences.index')
            ->with('success', 'Experiencia laboral actualizada correctamente.');
    }

    public function destroy(WorkExperience $workExperience)
    {
        if ($workExperience->user_id !== Auth::id()) {
            abort(403);
        }

        $workExperience->delete();

        return redirect()->route('work-experiences.index')
            ->with('success', 'Experiencia laboral eliminada correctamente.');
    }
}
