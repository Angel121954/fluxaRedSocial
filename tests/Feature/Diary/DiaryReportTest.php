<?php

declare(strict_types=1);

use App\Models\Diary;
use App\Models\DiaryReport;
use App\Models\DiaryResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function diaryReportContext(): array
{
    $owner = User::factory()->create(['onboarding_completed' => true]);
    $reporter = User::factory()->create(['onboarding_completed' => true]);

    $diary = Diary::create([
        'question' => '¿Qué aprendiste hoy?',
        'emoji' => '🔥',
        'status' => 'active',
    ]);

    $response = DiaryResponse::create([
        'diary_id' => $diary->id,
        'user_id' => $owner->id,
        'content' => 'Aprendí a reportar respuestas',
    ]);

    return [$owner, $reporter, $response];
}

describe('Reportar respuesta del diario', function () {
    it('impide reportar una respuesta propia', function () {
        [$owner, , $response] = diaryReportContext();

        $this->actingAs($owner)
            ->postJson(route('diary.response.report', $response), [
                'reason' => 'Quiero reportar mi propia respuesta',
            ])
            ->assertForbidden();

        expect(DiaryReport::count())->toBe(0);
    });

    it('permite reportar la respuesta de otro usuario', function () {
        [, $reporter, $response] = diaryReportContext();

        $this->actingAs($reporter)
            ->postJson(route('diary.response.report', $response), [
                'reason' => 'La respuesta contiene contenido inapropiado',
            ])
            ->assertOk();

        expect(DiaryReport::where('user_id', $reporter->id)
            ->where('diary_response_id', $response->id)
            ->exists())->toBeTrue();
    });
});
