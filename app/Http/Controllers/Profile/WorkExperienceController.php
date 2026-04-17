<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkExperienceRequest;
use App\Http\Requests\UpdateWorkExperienceRequest;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\Auth;

class WorkExperienceController extends Controller
{
    private function getLimits(): array
    {
        return [
            'max_work_experiences' => 5,
            'max_current_jobs' => 2,
        ];
    }

    public function index()
    {
        $experiences = WorkExperience::where('user_id', Auth::id())
            ->orderBy('started_at', 'desc')
            ->get();

        $limits = $this->getLimits();

        return view('profile.work-experiences', compact('experiences', 'limits'));
    }

    public function store(StoreWorkExperienceRequest $request)
    {
        $limits = $this->getLimits();
        $totalCount = WorkExperience::where('user_id', Auth::id())->count();

        if ($totalCount >= $limits['max_work_experiences']) {
            return back()
                ->withInput()
                ->withErrors(['company' => "Has alcanzado el límite de {$limits['max_work_experiences']} experiencias laborales."]);
        }

        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['current'] = $request->boolean('current');

        if ($validated['current']) {
            $validated['ended_at'] = null;
        }

        WorkExperience::create($validated);

        return redirect()->route('work-experiences.index')
            ->with('success', 'Experiencia laboral agregada correctamente.');
    }

    public function update(UpdateWorkExperienceRequest $request, WorkExperience $workExperience)
    {
        if ($workExperience->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();
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
