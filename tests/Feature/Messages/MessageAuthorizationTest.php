<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function messageUser(): User
{
    $user = User::factory()->create(['onboarding_completed' => true]);
    Profile::factory()->for($user)->create(['accept_messages' => true]);

    return $user;
}

describe('Autorización de mensajes', function () {
    it('rechaza enviar un mensaje a una conversación ajena', function () {
        $userA = messageUser();
        $userB = messageUser();
        $intruder = messageUser();

        $conversation = Conversation::create([
            'user_a_id' => $userA->id,
            'user_b_id' => $userB->id,
        ]);

        $this->actingAs($intruder)
            ->postJson(route('messages.store', $conversation), ['body' => 'Hola'])
            ->assertForbidden();

        expect(Message::count())->toBe(0);
    });

    it('permite enviar un mensaje a un participante de la conversación', function () {
        $userA = messageUser();
        $userB = messageUser();

        $conversation = Conversation::create([
            'user_a_id' => $userA->id,
            'user_b_id' => $userB->id,
        ]);

        $this->actingAs($userA)
            ->postJson(route('messages.store', $conversation), ['body' => 'Hola'])
            ->assertOk();

        expect(Message::where('conversation_id', $conversation->id)->count())->toBe(1);
    });

    it('rechaza adjuntar archivos con formatos no permitidos', function () {
        $userA = messageUser();
        $userB = messageUser();

        $conversation = Conversation::create([
            'user_a_id' => $userA->id,
            'user_b_id' => $userB->id,
        ]);

        $this->actingAs($userA)
            ->postJson(route('messages.storeMedia', $conversation), [
                'media_type' => 'file',
                'file' => UploadedFile::fake()->create('payload.html', 10, 'text/html'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file');

        expect(Message::count())->toBe(0);
    });

    it('rechaza GIFs alojados fuera de Giphy', function () {
        $userA = messageUser();
        $userB = messageUser();

        $conversation = Conversation::create([
            'user_a_id' => $userA->id,
            'user_b_id' => $userB->id,
        ]);

        $this->actingAs($userA)
            ->postJson(route('messages.storeGif', $conversation), [
                'media_type' => 'gif',
                'gif_url' => 'https://evil.example/gif.gif',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('gif_url');

        expect(Message::count())->toBe(0);
    });
});
