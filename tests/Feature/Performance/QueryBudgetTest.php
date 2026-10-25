<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Diary;
use App\Models\DiaryResponse;
use App\Models\DiaryResponseBookmark;
use App\Models\DiaryResponseLike;
use App\Models\Notification;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use App\View\Composers\TopbarComposer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function queryBudgetUser(): User
{
    $user = User::factory()->create([
        'onboarding_completed' => true,
    ]);

    Profile::factory()->for($user)->create();

    return $user;
}

function queryBudgetCount(Closure $callback): int
{
    $topbarCache = new ReflectionProperty(TopbarComposer::class, 'cache');
    $topbarCache->setValue(null, null);

    DB::flushQueryLog();
    DB::enableQueryLog();

    try {
        $callback();

        return count(DB::getQueryLog());
    } finally {
        DB::disableQueryLog();
    }
}

function queryBudgetDiaryResponses(Diary $diary, User $viewer, int $count): void
{
    for ($i = 0; $i < $count; $i++) {
        $author = queryBudgetUser();

        $response = DiaryResponse::create([
            'diary_id' => $diary->id,
            'user_id' => $author->id,
            'content' => "Respuesta {$i}",
        ]);

        DiaryResponseLike::create([
            'diary_response_id' => $response->id,
            'user_id' => $viewer->id,
        ]);

        DiaryResponseBookmark::create([
            'diary_response_id' => $response->id,
            'user_id' => $viewer->id,
        ]);
    }
}

function queryBudgetFeedProjects(User $viewer, int $count): void
{
    for ($i = 0; $i < $count; $i++) {
        $author = queryBudgetUser();

        $viewer->follows()->attach($author->id);

        Project::factory()->public()->create([
            'user_id' => $author->id,
            'title' => "Proyecto de prueba {$i}",
        ]);
    }
}

function queryBudgetProjectComments(Project $project, User $viewer, int $count): void
{
    for ($i = 0; $i < $count; $i++) {
        $author = queryBudgetUser();

        $comment = Comment::create([
            'user_id' => $author->id,
            'project_id' => $project->id,
            'content' => "Comentario {$i}",
        ]);

        $child = Comment::create([
            'user_id' => $author->id,
            'project_id' => $project->id,
            'parent_id' => $comment->id,
            'content' => "Respuesta {$i}",
        ]);

        CommentLike::create([
            'user_id' => $viewer->id,
            'comment_id' => $comment->id,
        ]);

        CommentLike::create([
            'user_id' => $viewer->id,
            'comment_id' => $child->id,
        ]);
    }
}

function queryBudgetNotifications(User $viewer, int $count): void
{
    for ($i = 0; $i < $count; $i++) {
        $fromUser = queryBudgetUser();

        Notification::create([
            'user_id' => $viewer->id,
            'type' => Notification::TYPE_FOLLOW,
            'title' => 'Nuevo seguidor',
            'body' => "Usuario {$i} comenzó a seguirte",
            'from_user_id' => $fromUser->id,
            'reference_id' => $fromUser->id,
            'reference_type' => 'user',
        ]);
    }
}

describe('Presupuesto de consultas', function () {
    it('mantiene estable el número de consultas al crecer las respuestas del diario', function () {
        $diary = Diary::create([
            'question' => '¿Qué aprendiste hoy?',
            'status' => 'active',
        ]);
        $viewer = queryBudgetUser();

        queryBudgetDiaryResponses($diary, $viewer, 3);
        $smallAuth = $viewer->fresh();
        $small = queryBudgetCount(fn () => $this->actingAs($smallAuth)
            ->get(route('diary.index'))
            ->assertOk());

        queryBudgetDiaryResponses($diary, $viewer, 7);
        $largeAuth = $viewer->fresh();
        $large = queryBudgetCount(fn () => $this->actingAs($largeAuth)
            ->get(route('diary.index'))
            ->assertOk());

        expect($large - $small)->toBeLessThanOrEqual(1);
    });

    it('mantiene estable el número de consultas al crecer el feed', function () {
        $viewer = queryBudgetUser();

        queryBudgetFeedProjects($viewer, 2);
        $smallAuth = $viewer->fresh();
        $small = queryBudgetCount(fn () => $this->actingAs($smallAuth)
            ->get(route('feed.index'))
            ->assertOk());

        queryBudgetFeedProjects($viewer, 6);
        $largeAuth = $viewer->fresh();
        $large = queryBudgetCount(fn () => $this->actingAs($largeAuth)
            ->get(route('feed.index'))
            ->assertOk());

        expect($large - $small)->toBeLessThanOrEqual(1);
    });

    it('mantiene estable el número de consultas al crecer los comentarios', function () {
        $viewer = queryBudgetUser();
        $project = Project::factory()->public()->create(['user_id' => $viewer->id]);

        queryBudgetProjectComments($project, $viewer, 3);
        $smallAuth = $viewer->fresh();
        $small = queryBudgetCount(fn () => $this->actingAs($smallAuth)
            ->getJson(route('comments.index', $project))
            ->assertOk());

        queryBudgetProjectComments($project, $viewer, 7);
        $largeAuth = $viewer->fresh();
        $large = queryBudgetCount(fn () => $this->actingAs($largeAuth)
            ->getJson(route('comments.index', $project))
            ->assertOk());

        expect($large - $small)->toBeLessThanOrEqual(1);
    });

    it('mantiene estable el número de consultas al crecer las notificaciones', function () {
        $viewer = queryBudgetUser();

        queryBudgetNotifications($viewer, 5);
        $smallAuth = $viewer->fresh();
        $small = queryBudgetCount(fn () => $this->actingAs($smallAuth)
            ->getJson(route('notifications.list'))
            ->assertOk());

        queryBudgetNotifications($viewer, 15);
        $largeAuth = $viewer->fresh();
        $large = queryBudgetCount(fn () => $this->actingAs($largeAuth)
            ->getJson(route('notifications.list'))
            ->assertOk());

        expect($large - $small)->toBeLessThanOrEqual(1);
    });
});
