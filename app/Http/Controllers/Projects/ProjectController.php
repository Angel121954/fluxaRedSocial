<?php

declare(strict_types=1);

namespace App\Http\Controllers\Projects;

use App\Exceptions\ContentModerationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreEndorsementRequest;
use App\Http\Requests\Project\StoreProjectReportRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectMedia;
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

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = $this->projectService->create($validated, Auth::id());

        if ($files = $request->file('media')) {
            try {
                $this->projectService->attachMedia($project, $files);
            } catch (ContentModerationException $e) {
                $this->projectService->delete($project);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['media' => [$e->getMessage()]],
                ], 422);
            }
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

        if (request()->wantsJson()) {
            $project->load(['media', 'technologies']);

            return response()->json($project);
        }

        return redirect()->route('projects.show', $project);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validated();

        $this->projectService->update($project, $validated);

        if ($files = $request->file('media')) {
            try {
                $this->projectService->attachMedia($project, $files);
            } catch (ContentModerationException $e) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'errors' => ['media' => [$e->getMessage()]],
                    ], 422);
                }

                return back()->withErrors(['media' => $e->getMessage()]);
            }
        }

        if ($request->wantsJson()) {
            $project->load(['media', 'technologies']);

            return response()->json([
                'success' => true,
                'message' => 'Proyecto actualizado.',
                'project' => $project,
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyecto actualizado.');
    }

    public function deleteMedia(Project $project, ProjectMedia $media)
    {
        $this->authorize('update', $project);

        if ($media->project_id !== $project->id) {
            abort(404);
        }

        $this->projectService->deleteProjectMedia($media);

        return response()->json(['success' => true]);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Proyecto eliminado.']);
        }

        return redirect()->route('explore.index')
            ->with('success', 'Proyecto eliminado.');
    }

    public function like(Project $project)
    {
        $this->authorize('view', $project);

        $result = $this->projectService->toggleLike($project, Auth::id());

        return response()->json($result);
    }

    public function bookmark(Project $project)
    {
        $this->authorize('view', $project);

        $result = $this->projectService->toggleBookmark($project, Auth::id());

        return response()->json($result);
    }

    public function report(StoreProjectReportRequest $request, Project $project)
    {
        $this->authorize('view', $project);

        $created = $this->projectService->reportProject(
            $project,
            Auth::id(),
            $request->validated('reason'),
        );

        if (! $created) {
            return response()->json([
                'message' => 'Ya enviaste un reporte para este proyecto. Lo estaremos revisando.',
            ]);
        }

        return response()->json([
            'message' => 'Reporte enviado. Gracias por ayudar a mantener la comunidad segura.',
        ]);
    }

    public function endorse(StoreEndorsementRequest $request, Project $project)
    {
        $this->authorize('view', $project);

        $result = $this->projectService->endorseProject(
            $project,
            Auth::id(),
            $request->validated('skill_type'),
        );

        return response()->json($result);
    }
}
