<?php

declare(strict_types=1);

use App\Models\Diary;
use App\Models\DiaryResponse;
use App\Models\DiaryResponseComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function diaryParentUser(): User
{
    return User::factory()->create(['onboarding_completed' => true]);
}

function diaryParentDiary(): Diary
{
    return Diary::create([
        'question' => '¿Qué aprendiste hoy?',
        'emoji' => '🔥',
        'status' => 'active',
    ]);
}

function diaryParentResponse(Diary $diary, User $user): DiaryResponse
{
    return DiaryResponse::create([
        'diary_id' => $diary->id,
        'user_id' => $user->id,
        'content' => 'Respuesta del diario',
    ]);
}

describe('Respuestas a comentarios del diario', function () {
    it('rechaza responder a un comentario de otra respuesta', function () {
        $commenter = diaryParentUser();
        $diary = diaryParentDiary();
        $response = diaryParentResponse($diary, diaryParentUser());
        $otherResponse = diaryParentResponse($diary, diaryParentUser());
        $foreignComment = DiaryResponseComment::create([
            'diary_response_id' => $otherResponse->id,
            'user_id' => $otherResponse->user_id,
            'content' => 'Comentario de otra respuesta',
        ]);

        $this->actingAs($commenter)
            ->postJson(route('diary.response.comment', $response), [
                'content' => 'Respuesta cruzada',
                'parent_id' => $foreignComment->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('parent_id');

        expect(DiaryResponseComment::where('diary_response_id', $response->id)->count())->toBe(0);
    });

    it('permite responder a un comentario de la misma respuesta', function () {
        $commenter = diaryParentUser();
        $diary = diaryParentDiary();
        $response = diaryParentResponse($diary, diaryParentUser());
        $parentComment = DiaryResponseComment::create([
            'diary_response_id' => $response->id,
            'user_id' => $response->user_id,
            'content' => 'Comentario original',
        ]);

        $this->actingAs($commenter)
            ->postJson(route('diary.response.comment', $response), [
                'content' => 'Respuesta válida',
                'parent_id' => $parentComment->id,
            ])
            ->assertOk();

        expect(DiaryResponseComment::where('diary_response_id', $response->id)
            ->where('parent_id', $parentComment->id)
            ->exists())->toBeTrue();
    });
});
