<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        $user = Auth::user();

        $projects = Project::with('media', 'technologies')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        Log::info('Iniciando creacion de proyecto', [
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'privacy' => $validated['privacy'] ?? 'public',
            'techs_count' => is_array($validated['techs'] ?? []) ? count($validated['techs']) : 0,
            'media_count' => $request->hasFile('media') ? count($request->file('media')) : 0,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $project = $this->projectService->create($validated, Auth::id());

        if ($request->hasFile('media')) {
            $this->projectService->attachMedia($project, $request->file('media'));
        }

        Log::info('Proyecto publicado correctamente', [
            'project_id' => $project->id,
            'user_id' => $project->user_id,
            'title' => $project->title,
            'media_total' => $project->media->count(),
            'techs_total' => $project->technologies->count(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proyecto publicado correctamente.',
            'project' => $project->load('media', 'technologies'),
        ]);
    }

    public function show(Project $project)
    {
        $project->load('media', 'technologies');

        return view('explore.index', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $project->load('media', 'technologies');

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
        $user = Auth::user();

        $existingLike = ProjectLike::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $project->decrement('likes_count');
            $isLiked = false;
        } else {
            ProjectLike::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);
            $project->increment('likes_count');
            $isLiked = true;
        }

        return response()->json([
            'likes_count' => $project->likes_count,
            'is_liked' => $isLiked,
        ]);
    }
}
