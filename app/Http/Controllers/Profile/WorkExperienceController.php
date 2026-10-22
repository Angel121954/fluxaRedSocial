<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreWorkExperienceRequest;
use App\Http\Requests\Profile\UpdateWorkExperienceRequest;
use App\Models\Technology;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        // Se cargan las tecnologías de cada experiencia para pintar los chips
        // y para que el modal de edición sepa cuáles vienen seleccionadas.
        $experiences = WorkExperience::where('user_id', Auth::id())
            ->with('technologies:id,name,slug')
            ->orderBy('started_at', 'desc')
            ->get();

        // Misma caché que usa el perfil para no duplicar consultas pesadas.
        $allTechnologies = Cache::remember('all_technologies', 3600, fn () => Technology::select('id', 'name', 'slug', 'category')
            ->orderBy('category')->orderBy('name')->get()
        );

        $limits = $this->getLimits();

        return view('profile.work-experiences', compact('experiences', 'limits', 'allTechnologies'));
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

        // technologies no es columna de work_experiences: se guarda en el pivot.
        $technologies = $validated['technologies'] ?? [];
        unset($validated['technologies']);

        $validated['user_id'] = Auth::id();
        $validated['current'] = $request->boolean('current');

        if ($validated['current']) {
            $validated['ended_at'] = null;
        }

        $experience = WorkExperience::create($validated);
        $experience->technologies()->sync($technologies);

        return redirect()->route('work-experiences.index')
            ->with('success', 'Experiencia laboral agregada correctamente.');
    }

    public function update(UpdateWorkExperienceRequest $request, WorkExperience $workExperience)
    {
        $this->authorize('update', $workExperience);

        $validated = $request->validated();

        $technologies = $validated['technologies'] ?? [];
        unset($validated['technologies']);

        $validated['current'] = $request->boolean('current');

        if ($validated['current']) {
            $validated['ended_at'] = null;
        }

        $workExperience->update($validated);
        // sync reemplaza la selección anterior sin dejar filas huérfanas.
        $workExperience->technologies()->sync($technologies);

        return redirect()->route('work-experiences.index')
            ->with('success', 'Experiencia laboral actualizada correctamente.');
    }

    public function destroy(WorkExperience $workExperience)
    {
        $this->authorize('delete', $workExperience);

        $workExperience->delete();

        return redirect()->route('work-experiences.index')
            ->with('success', 'Experiencia laboral eliminada correctamente.');
    }
}
