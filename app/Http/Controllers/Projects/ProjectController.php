<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\SkillEndorsement;
use App\Models\Technology;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        $project->load([
            'user.profile',
            'media',
            'technologies',
            'likes',
            'bookmarks',
            'skillEndorsements',
        ]);
        $profile = Profile::where('user_id', Auth::id())->first();

        $projects = new Collection([$project]);

        $topTechnologies = Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->take(15)
            ->get();

        return view('explore.index', compact('project', 'profile', 'projects', 'topTechnologies'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $project->load([
            'user.profile',
            'media',
            'technologies',
            'likes',
            'bookmarks',
            'skillEndorsements',
        ]);

        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validated();

        $this->projectService->update($project, $validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyecto actualizado.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto eliminado.');
    }

    public function like(Project $project)
    {
        $result = $this->projectService->toggleLike($project, Auth::id());
        
        return response()->json($result);
    }

    public function bookmark(Project $project)
    {
        $result = $this->projectService->toggleBookmark($project, Auth::id());
        
        return response()->json($result);
    }

    public function report(Request $request, Project $project)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $repeatedReport = ProjectReport::where('project_id', $project->id)
            ->where('user_id', Auth::id())->first();

        if ($repeatedReport) {
            return response()->json([
                'message' => 'Ya enviaste 1 reporte paraesté proyecto. Lo estaremos revisando.',
            ]);
        }

        ProjectReport::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Reporte enviado. Gracias por ayudar a mantener la comunidad segura.',
        ]);
    }

    public function endorse(Request $request, Project $project)
    {
        $validated = $request->validate([
            'skill_type' => 'required|string|in:'.implode(',', array_keys(SkillEndorsement::SKILLS)),
        ]);

        try {
            $result = $this->projectService->endorseProject(
                $project,
                Auth::id(),
                $validated['skill_type']
            );
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }
    }
}
