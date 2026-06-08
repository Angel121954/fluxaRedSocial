<?php

declare(strict_types=1);

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreEndorsementRequest;
use App\Http\Requests\Project\StoreProjectReportRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Services\ProjectService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        return redirect()->route('explore.index');
    }

    public function create()
    {
        return redirect()->route('explore.index');
    }

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = $this->projectService->create($validated, Auth::id());

        if ($files = $request->file('media')) {
            $this->projectService->attachMedia($project, $files);
        }

        return response()->json([
            'success' => true,
            'message' => 'Proyecto publicado',
            'project' => $project,
        ], 201);
    }

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

        $projects = new Collection([$project]);

        return view('explore.index', compact('project', 'projects'));
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

        if ($files = $request->file('media')) {
            $this->projectService->attachMedia($project, $files);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyecto actualizado.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Proyecto eliminado.']);
        }

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

    public function report(StoreProjectReportRequest $request, Project $project)
    {
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
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Reporte enviado. Gracias por ayudar a mantener la comunidad segura.',
        ]);
    }

    public function endorse(StoreEndorsementRequest $request, Project $project)
    {

        try {
            $result = $this->projectService->endorseProject(
                $project,
                Auth::id(),
                $request->skill_type
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }
    }
}
