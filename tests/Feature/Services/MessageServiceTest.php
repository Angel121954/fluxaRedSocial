<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserBlock;
use App\Services\CloudinaryService;
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

function msgCreateUser(array $userOverrides = [], array $profileOverrides = []): User
{
    $user = User::factory()->create($userOverrides);
    Profile::factory()->create(array_merge([
        'user_id' => $user->id,
        'accept_messages' => true,
    ], $profileOverrides));

    return $user;
}

function msgService(): MessageService
{
    $cloudinary = Mockery::mock(CloudinaryService::class);

    return new MessageService($cloudinary);
}

describe('MessageService', function () {
    describe('findOrCreateConversation', function () {
        it('crea una nueva conversacion cuando no existe', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            $conversation = msgService()->findOrCreateConversation($userA->id, $userB->id);

            expect($conversation)->toBeInstanceOf(Conversation::class)
                ->and($conversation->user_a_id)->toBe($userA->id)
                ->and($conversation->user_b_id)->toBe($userB->id)
                ->and(Conversation::count())->toBe(1);
        });

        it('retorna la conversacion existente sin crear duplicado', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            $first = msgService()->findOrCreateConversation($userA->id, $userB->id);
            $second = msgService()->findOrCreateConversation($userA->id, $userB->id);

            expect($first->id)->toBe($second->id)
                ->and(Conversation::count())->toBe(1);
        });

        it('encuentra la conversacion con los IDs en orden inverso', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            msgService()->findOrCreateConversation($userA->id, $userB->id);
            $result = msgService()->findOrCreateConversation($userB->id, $userA->id);

            expect($result->user_a_id)->toBe($userA->id)
                ->and($result->user_b_id)->toBe($userB->id);
        });
    });

    describe('canSendMessage', function () {
        it('retorna true cuando el destinatario acepta mensajes y no hay bloqueo', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser([], ['accept_messages' => true]);

            expect(msgService()->canSendMessage($sender->id, $recipient->id))->toBeTrue();
        });

        it('retorna false cuando el destinatario no acepta mensajes', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser([], ['accept_messages' => false]);

            expect(msgService()->canSendMessage($sender->id, $recipient->id))->toBeFalse();
        });

        it('retorna false cuando el remitente esta bloqueado por el destinatario', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();

            UserBlock::create([
                'blocker_id' => $recipient->id,
                'blocked_id' => $sender->id,
            ]);

            expect(msgService()->canSendMessage($sender->id, $recipient->id))->toBeFalse();
        });
    });

    describe('isBlockedBy / hasBlocked', function () {
        it('isBlockedBy retorna true cuando el usuario esta bloqueado', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            UserBlock::create(['blocker_id' => $userB->id, 'blocked_id' => $userA->id]);

            expect(msgService()->isBlockedBy($userA->id, $userB->id))->toBeTrue();
        });

        it('hasBlocked retorna true cuando el usuario bloqueo a otro', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            UserBlock::create(['blocker_id' => $userA->id, 'blocked_id' => $userB->id]);

            expect(msgService()->hasBlocked($userA->id, $userB->id))->toBeTrue();
        });
    });

    describe('blockUser / unblockUser', function () {
        it('bloquea a un usuario', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            msgService()->blockUser($userA->id, $userB->id);

            expect(UserBlock::count())->toBe(1)
                ->and(UserBlock::first()->blocker_id)->toBe($userA->id)
                ->and(UserBlock::first()->blocked_id)->toBe($userB->id);
        });

        it('desbloquea a un usuario', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            msgService()->blockUser($userA->id, $userB->id);
            msgService()->unblockUser($userA->id, $userB->id);

            expect(UserBlock::count())->toBe(0);
        });

        it('no crea bloqueo duplicado', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();

            msgService()->blockUser($userA->id, $userB->id);
            msgService()->blockUser($userA->id, $userB->id);

            expect(UserBlock::count())->toBe(1);
        });
    });

    describe('sendMessage', function () {
        it('crea un mensaje en la conversacion', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);

            $message = msgService()->sendMessage($conversation, $sender->id, 'Hola, mundo!');

            expect($message)->toBeInstanceOf(Message::class)
                ->and($message->conversation_id)->toBe($conversation->id)
                ->and($message->sender_id)->toBe($sender->id)
                ->and($message->body)->toBe('Hola, mundo!')
                ->and(Message::count())->toBe(1);
        });

        it('carga la relacion sender', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);

            $message = msgService()->sendMessage($conversation, $sender->id, 'Test');

            expect($message->relationLoaded('sender'))->toBeTrue()
                ->and($message->sender->id)->toBe($sender->id);
        });
    });

    describe('sendGifMessage', function () {
        it('sube el GIF a Cloudinary y crea el mensaje', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);

            $cloudinary = Mockery::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('uploadFromUrl')
                ->once()
                ->with('https://ejemplo.com/gif.gif', 'fluxa/messages/gifs')
                ->andReturn([
                    'secure_url' => 'https://cloudinary.com/gif.gif',
                    'public_id' => 'gifs/abc123',
                    'bytes' => 12345,
                ]);

            $service = new MessageService($cloudinary);
            $message = $service->sendGifMessage($conversation, $sender->id, 'https://ejemplo.com/gif.gif', 'Un GIF');

            expect($message->media_type)->toBe('gif')
                ->and($message->media_url)->toBe('https://cloudinary.com/gif.gif')
                ->and($message->public_id)->toBe('gifs/abc123')
                ->and($message->body)->toBe('Un GIF')
                ->and($message->relationLoaded('sender'))->toBeTrue();
        });
    });

    describe('sendMediaMessage', function () {
        it('sube el archivo a Cloudinary y crea el mensaje', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);

            $cloudinary = Mockery::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('uploadMessageMedia')
                ->once()
                ->andReturn([
                    'secure_url' => 'https://cloudinary.com/image.jpg',
                    'public_id' => 'messages/xyz789',
                ]);

            $file = UploadedFile::fake()->image('foto.jpg', 200, 200);

            $service = new MessageService($cloudinary);
            $message = $service->sendMediaMessage($conversation, $sender->id, $file, 'image', 'Mi foto');

            expect($message->media_type)->toBe('image')
                ->and($message->media_url)->toBe('https://cloudinary.com/image.jpg')
                ->and($message->media_name)->toBe('foto.jpg')
                ->and($message->body)->toBe('Mi foto')
                ->and($message->relationLoaded('sender'))->toBeTrue();
        });

        it('permite enviar sin body', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);

            $cloudinary = Mockery::mock(CloudinaryService::class);
            $cloudinary->shouldReceive('uploadMessageMedia')
                ->once()
                ->andReturn([
                    'secure_url' => 'https://cloudinary.com/file.pdf',
                    'public_id' => 'messages/pdf123',
                ]);

            $file = UploadedFile::fake()->create('doc.pdf', 100);

            $service = new MessageService($cloudinary);
            $message = $service->sendMediaMessage($conversation, $sender->id, $file, 'file');

            expect($message->body)->toBeNull();
        });
    });

    describe('markConversationAsRead', function () {
        it('marca todos los mensajes no leidos como leidos', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);

            msgService()->sendMessage($conversation, $sender->id, 'Msg 1');
            msgService()->sendMessage($conversation, $sender->id, 'Msg 2');

            msgService()->markConversationAsRead($conversation, $recipient->id);

            $unread = Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $recipient->id)
                ->whereNull('read_at')
                ->count();

            expect($unread)->toBe(0);
        });

        it('no marca los mensajes del propio usuario como leidos', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);

            msgService()->sendMessage($conversation, $sender->id, 'Msg 1');

            msgService()->markConversationAsRead($conversation, $sender->id);

            $unread = Message::where('conversation_id', $conversation->id)
                ->whereNull('read_at')
                ->count();

            expect($unread)->toBe(1);
        });
    });

    describe('markMessageAsRead', function () {
        it('marca un mensaje como leido', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);
            $message = msgService()->sendMessage($conversation, $sender->id, 'Test');

            $result = msgService()->markMessageAsRead($message, $recipient->id);

            expect($result)->toBeTrue()
                ->and($message->fresh()->read_at)->not->toBeNull();
        });

        it('retorna false si el lector es el remitente', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);
            $message = msgService()->sendMessage($conversation, $sender->id, 'Test');

            $result = msgService()->markMessageAsRead($message, $sender->id);

            expect($result)->toBeFalse()
                ->and($message->fresh()->read_at)->toBeNull();
        });
    });

    describe('autoReadIfViewing', function () {
        it('marca como leido si el destinatario esta viendo la conversacion', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);
            $message = msgService()->sendMessage($conversation, $sender->id, 'Test');

            Cache::put("user.{$recipient->id}.viewing_conv", $conversation->id, now()->addMinutes(2));

            msgService()->autoReadIfViewing($message, $conversation->id, $recipient->id);

            expect($message->fresh()->read_at)->not->toBeNull();
        });

        it('no marca como leido si no esta viendo la conversacion', function () {
            $sender = msgCreateUser();
            $recipient = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($sender->id, $recipient->id);
            $message = msgService()->sendMessage($conversation, $sender->id, 'Test');

            msgService()->autoReadIfViewing($message, $conversation->id, $recipient->id);

            expect($message->fresh()->read_at)->toBeNull();
        });
    });

    describe('setViewingConversation / clearViewingConversation', function () {
        it('guarda y limpia la conversacion activa en cache', function () {
            $user = msgCreateUser();

            msgService()->setViewingConversation($user->id, 42);
            expect(Cache::get("user.{$user->id}.viewing_conv"))->toBe(42);

            msgService()->clearViewingConversation($user->id);
            expect(Cache::get("user.{$user->id}.viewing_conv"))->toBeNull();
        });
    });

    describe('getExistingConversationUserIds', function () {
        it('retorna los IDs de usuarios con conversacion existente', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();
            $userC = msgCreateUser();

            msgService()->findOrCreateConversation($userA->id, $userB->id);

            $ids = msgService()->getExistingConversationUserIds($userA->id);

            expect($ids)->toBe([$userB->id])
                ->and($ids)->not->toContain($userC->id);
        });

        it('retorna array vacio si no hay conversaciones', function () {
            $user = msgCreateUser();

            expect(msgService()->getExistingConversationUserIds($user->id))->toBe([]);
        });
    });

    describe('getUserConversations', function () {
        it('retorna las conversaciones del usuario con metadatos', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($userA->id, $userB->id);
            msgService()->sendMessage($conversation, $userB->id, 'Hola!');

            $conversations = msgService()->getUserConversations($userA->id);

            expect($conversations)->toHaveCount(1);
            $conv = $conversations->first();
            expect($conv->id)->toBe($conversation->id)
                ->and($conv->relationLoaded('otherChat'))->toBeTrue()
                ->and($conv->otherChat->id)->toBe($userB->id);
        });

        it('incluye el conteo de no leidos', function () {
            $userA = msgCreateUser();
            $userB = msgCreateUser();
            $conversation = msgService()->findOrCreateConversation($userA->id, $userB->id);
            msgService()->sendMessage($conversation, $userB->id, 'No leido');

            $conversations = msgService()->getUserConversations($userA->id);

            expect((int) $conversations->first()->unread_count)->toBe(1);
        });
    });
});
