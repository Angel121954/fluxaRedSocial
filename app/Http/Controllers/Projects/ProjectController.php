<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectBookmark;
use App\Models\ProjectLike;
use App\Models\ProjectReport;
use App\Models\SkillEndorsement;
use App\Models\User;
use App\Models\Profile;
use App\Models\Notification;
use App\Notifications\CreatesNotifications;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{

public function show(Project $project)
    {
        $project->load('media', 'technologies');
        $profile = Profile::where('user_id', Auth::id())->first();
        
        $projects = new Collection([$project]);
        
        $topTechnologies = \App\Models\Technology::withCount('projects')
            ->orderByDesc('projects_count')
            ->take(15)
            ->get();

        return view('explore.index', compact('project', 'profile', 'projects', 'topTechnologies'));
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

            if ($project->user_id !== $user->id) {
                CreatesNotifications::notifyProjectLike(
                    $project->user_id,
                    $user->id,
                    $user->name,
                    $project->id,
                    $project->title
                );
            }
        }

        return response()->json([
            'likes_count' => $project->likes_count,
            'is_liked' => $isLiked,
        ]);
    }

    public function bookmark(Project $project)
    {
        $user = Auth::user();

        $existingBookmark = ProjectBookmark::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            $isBookmarked = false;
        } else {
            ProjectBookmark::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);
            $isBookmarked = true;
        }

        return response()->json([
            'is_bookmarked' => $isBookmarked,
        ]);
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
            'skill_type' => 'required|string|in:' . implode(',', array_keys(SkillEndorsement::SKILLS)),
        ]);

        $user = Auth::user();

        if ($project->user_id === $user->id) {
            return response()->json([
                'message' => 'No puedes recomendar las habilidades de tu propio proyecto.',
            ], 403);
        }

        $currentEndorsement = SkillEndorsement::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        $isNewEndorsement = false;

        if ($currentEndorsement) {
            if ($currentEndorsement->skill_type === $validated['skill_type']) {
                $currentEndorsement->delete();
                $isEndorsed = false;
                $userEndorsement = null;
            } else {
                $currentEndorsement->delete();
                SkillEndorsement::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'skill_type' => $validated['skill_type'],
                ]);
                $isEndorsed = true;
                $userEndorsement = $validated['skill_type'];
                $isNewEndorsement = true;
            }
        } else {
            SkillEndorsement::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'skill_type' => $validated['skill_type'],
            ]);
            $isEndorsed = true;
            $userEndorsement = $validated['skill_type'];
            $isNewEndorsement = true;
        }

        if ($isNewEndorsement && $project->user_id !== $user->id) {
            CreatesNotifications::notifyEndorsement(
                $project->user_id,
                $user->id,
                $user->name,
                $project->id,
                $project->title,
                $validated['skill_type']
            );
        }

        $skillCounts = SkillEndorsement::getSkillCounts($project->id);
        $dbUserEndorsement = SkillEndorsement::getUserEndorsement($user->id, $project->id);

        return response()->json([
            'skill_counts' => $skillCounts,
            'user_endorsement' => $dbUserEndorsement,
            'is_endorsed' => $isEndorsed,
        ]);
    }
}
