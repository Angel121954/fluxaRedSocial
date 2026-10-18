<?php

declare(strict_types=1);

use App\Models\Diary;
use App\Models\DiaryReport;
use App\Models\DiaryResponse;
use App\Models\DiaryResponseBookmark;
use App\Models\DiaryResponseComment;
use App\Models\DiaryResponseCommentLike;
use App\Models\DiaryResponseLike;
use App\Models\Notification;
use App\Models\Profile;
use App\Models\User;
use App\Services\DiaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

function diaryUser(): User
{
    $user = User::factory()->create();
    Profile::factory()->create(['user_id' => $user->id]);

    return $user;
}

function diaryService(): DiaryService
{
    return new DiaryService;
}

function activeDiary(): Diary
{
    return Diary::create([
        'question' => '¿Qué aprendiste hoy?',
        'emoji' => '🔥',
        'status' => 'active',
    ]);
}

function diaryResponse(Diary $diary, User $user, string $content = 'Aprendí testing'): DiaryResponse
{
    return DiaryResponse::create([
        'diary_id' => $diary->id,
        'user_id' => $user->id,
        'content' => $content,
    ]);
}

describe('DiaryService', function () {
    describe('getActiveDiary', function () {
        it('retorna el diario activo', function () {
            $diary = activeDiary();

            expect(diaryService()->getActiveDiary()?->id)->toBe($diary->id);
        });

        it('retorna null cuando no hay diario activo', function () {
            Diary::create(['question' => 'Cerrado', 'emoji' => '❌', 'status' => 'closed']);

            expect(diaryService()->getActiveDiary())->toBeNull();
        });
    });

    describe('createOrUpdateResponse', function () {
        it('crea una respuesta nueva', function () {
            $user = diaryUser();
            $diary = activeDiary();

            $response = diaryService()->createOrUpdateResponse($diary, $user->id, 'Mi respuesta');

            expect($response->content)->toBe('Mi respuesta')
                ->and($response->user_id)->toBe($user->id)
                ->and(DiaryResponse::count())->toBe(1);
        });

        it('actualiza la respuesta existente del mismo usuario', function () {
            $user = diaryUser();
            $diary = activeDiary();
            diaryResponse($diary, $user, 'Original');

            $response = diaryService()->createOrUpdateResponse($diary, $user->id, 'Actualizada');

            expect($response->content)->toBe('Actualizada')
                ->and(DiaryResponse::count())->toBe(1);
        });
    });

    describe('toggleLike', function () {
        it('agrega un like cuando no existe', function () {
            $owner = diaryUser();
            $liker = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            Event::fake();

            $result = diaryService()->toggleLike($response, $liker->id);

            expect($result['liked'])->toBeTrue()
                ->and($result['likes_count'])->toBe(1)
                ->and(DiaryResponseLike::count())->toBe(1);
        });

        it('quita el like cuando ya existe', function () {
            $owner = diaryUser();
            $liker = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);
            DiaryResponseLike::create(['diary_response_id' => $response->id, 'user_id' => $liker->id]);
            $response->likes_count = 1;
            $response->save();

            $result = diaryService()->toggleLike($response, $liker->id);

            expect($result['liked'])->toBeFalse()
                ->and($result['likes_count'])->toBe(0)
                ->and(DiaryResponseLike::count())->toBe(0);
        });

        it('notifica al dueño cuando otro usuario da like', function () {
            $owner = diaryUser();
            $liker = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            Event::fake();

            diaryService()->toggleLike($response, $liker->id);

            expect(Notification::count())->toBe(1)
                ->and(Notification::first()->user_id)->toBe($owner->id);
        });

        it('no notifica cuando el dueño se da like a si mismo', function () {
            $owner = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            Event::fake();

            diaryService()->toggleLike($response, $owner->id);

            expect(Notification::count())->toBe(0);
        });
    });

    describe('toggleBookmark', function () {
        it('agrega un bookmark cuando no existe', function () {
            $owner = diaryUser();
            $saver = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            Event::fake();

            $result = diaryService()->toggleBookmark($response, $saver->id);

            expect($result['bookmarked'])->toBeTrue()
                ->and(DiaryResponseBookmark::count())->toBe(1);
        });

        it('quita el bookmark cuando ya existe', function () {
            $owner = diaryUser();
            $saver = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);
            DiaryResponseBookmark::create(['diary_response_id' => $response->id, 'user_id' => $saver->id]);

            $result = diaryService()->toggleBookmark($response, $saver->id);

            expect($result['bookmarked'])->toBeFalse()
                ->and(DiaryResponseBookmark::count())->toBe(0);
        });
    });

    describe('createComment', function () {
        it('crea un comentario e incrementa el contador', function () {
            $owner = diaryUser();
            $commenter = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            Event::fake();

            $result = diaryService()->createComment($response, $commenter->id, 'Buen aporte', null);

            expect($result['comment'])->toBeInstanceOf(DiaryResponseComment::class)
                ->and($result['comments_count'])->toBe(1)
                ->and($response->fresh()->comments_count)->toBe(1);
        });

        it('notifica al dueño de la respuesta', function () {
            $owner = diaryUser();
            $commenter = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            Event::fake();

            diaryService()->createComment($response, $commenter->id, 'Comentario', null);

            expect(Notification::count())->toBe(1)
                ->and(Notification::first()->user_id)->toBe($owner->id);
        });

        it('no notifica cuando el dueño comenta su propia respuesta', function () {
            $owner = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            Event::fake();

            diaryService()->createComment($response, $owner->id, 'Comentario propio', null);

            expect(Notification::count())->toBe(0);
        });
    });

    describe('toggleCommentLike', function () {
        it('agrega un like al comentario', function () {
            $owner = diaryUser();
            $liker = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);
            $comment = DiaryResponseComment::create([
                'diary_response_id' => $response->id,
                'user_id' => $owner->id,
                'content' => 'Comentario',
            ]);

            $liked = diaryService()->toggleCommentLike($comment, $liker->id);

            expect($liked)->toBeTrue()
                ->and(DiaryResponseCommentLike::count())->toBe(1);
        });

        it('quita el like si ya existe', function () {
            $owner = diaryUser();
            $liker = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);
            $comment = DiaryResponseComment::create([
                'diary_response_id' => $response->id,
                'user_id' => $owner->id,
                'content' => 'Comentario',
            ]);
            DiaryResponseCommentLike::create([
                'diary_response_comment_id' => $comment->id,
                'user_id' => $liker->id,
            ]);

            $liked = diaryService()->toggleCommentLike($comment, $liker->id);

            expect($liked)->toBeFalse()
                ->and(DiaryResponseCommentLike::count())->toBe(0);
        });
    });

    describe('deleteComment', function () {
        it('elimina el comentario y sus respuestas ajustando el contador', function () {
            $owner = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);
            $response->comments_count = 2;
            $response->save();

            $comment = DiaryResponseComment::create([
                'diary_response_id' => $response->id,
                'user_id' => $owner->id,
                'content' => 'Padre',
            ]);
            DiaryResponseComment::create([
                'diary_response_id' => $response->id,
                'user_id' => $owner->id,
                'parent_id' => $comment->id,
                'content' => 'Hijo',
            ]);

            $count = diaryService()->deleteComment($comment);

            expect($count)->toBe(0)
                ->and(DiaryResponseComment::count())->toBe(0);
        });
    });

    describe('report', function () {
        it('crea el reporte la primera vez', function () {
            $owner = diaryUser();
            $reporter = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);

            $created = diaryService()->report($response, $reporter->id, 'Spam');

            expect($created)->toBeTrue()
                ->and(DiaryReport::count())->toBe(1);
        });

        it('retorna false si el usuario ya reporto', function () {
            $owner = diaryUser();
            $reporter = diaryUser();
            $response = diaryResponse(activeDiary(), $owner);
            DiaryReport::create([
                'user_id' => $reporter->id,
                'diary_response_id' => $response->id,
                'reason' => 'Spam',
            ]);

            $created = diaryService()->report($response, $reporter->id, 'Spam');

            expect($created)->toBeFalse()
                ->and(DiaryReport::count())->toBe(1);
        });
    });
});
