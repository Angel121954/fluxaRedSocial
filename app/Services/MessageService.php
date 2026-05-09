<?php

namespace App\Services;

use App\Events\ConversationCreated;
use App\Events\NewMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MessageService
{
    public function findOrCreateConversation(int $userId, int $otherUserId): Conversation
    {
        $conversation = Conversation::where(function ($query) use ($userId, $otherUserId) {
            $query->where(function ($q) use ($userId, $otherUserId) {
                $q->where('user_a_id', $userId)->where('user_b_id', $otherUserId);
            })->orWhere(function ($q) use ($userId, $otherUserId) {
                $q->where('user_a_id', $otherUserId)->where('user_b_id', $userId);
            });
        })->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_a_id' => $userId,
                'user_b_id' => $otherUserId,
            ]);
        }

        return $conversation;
    }

    public function canSendMessage(int $senderId, int $recipientId): bool
    {
        $recipientProfile = Profile::where('user_id', $recipientId)->first();

        return $recipientProfile && $recipientProfile->accept_messages;
    }

    public function sendMessage(Conversation $conversation, int $senderId, string $body): Message
    {
        $message = $conversation->messages()->create([
            'sender_id' => $senderId,
            'body' => $body,
        ]);

        $message->load('sender');

        return $message;
    }

    public function autoReadIfViewing(Message $message, int $conversationId, int $recipientId): void
    {
        $recipientViewing = Cache::get("user.{$recipientId}.viewing_conv") === $conversationId;

        if ($recipientViewing) {
            $message->update(['read_at' => now()]);
        }
    }

    public function broadcastNewMessage(Message $message, int $conversationId, int $recipientId): void
    {
        try {
            broadcast(new NewMessage($message, $conversationId, $recipientId))->toOthers();
        } catch (\Exception $e) {
            Log::error('Broadcast failed: ' . $e->getMessage());
        }
    }

    public function broadcastConversationCreated(Conversation $conversation, int $recipientId, ?Message $message = null): void
    {
        try {
            broadcast(new ConversationCreated($conversation, $recipientId, $message));
            Log::info('ConversationCreated broadcast', [
                'conversation_id' => $conversation->id,
                'otherUserId' => $recipientId,
            ]);
        } catch (\Exception $e) {
            Log::error('Broadcast error (new conversation)', ['error' => $e->getMessage()]);
        }
    }

    public function markConversationAsRead(Conversation $conversation, int $userId): void
    {
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markMessageAsRead(Message $message, int $userId): bool
    {
        if ($message->sender_id === $userId) {
            return false;
        }

        if ($message->read_at === null) {
            $message->update(['read_at' => now()]);
        }

        return true;
    }

    public function setViewingConversation(int $userId, int $conversationId): void
    {
        Cache::put("user.{$userId}.viewing_conv", $conversationId, now()->addMinutes(2));
    }

    public function clearViewingConversation(int $userId): void
    {
        Cache::forget("user.{$userId}.viewing_conv");
    }

    public function getExistingConversationUserIds(int $userId): array
    {
        return Conversation::where('user_a_id', $userId)
            ->orWhere('user_b_id', $userId)
            ->select('user_a_id', 'user_b_id')
            ->get()
            ->map(function ($conv) use ($userId) {
                return $conv->user_a_id === $userId ? $conv->user_b_id : $conv->user_a_id;
            })
            ->unique()
            ->toArray();
    }

    public function getUserConversations(int $userId, ?int $activeConversationId = null): \Illuminate\Support\Collection
    {
        $conversations = Conversation::with(['userA', 'userB'])
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('sender_id', '!=', $userId)
                  ->whereNull('read_at');
            }])
            ->where(function ($q) use ($userId) {
                $q->where('user_a_id', $userId)->orWhere('user_b_id', $userId);
            })
            ->get()
            ->sortByDesc(function ($c) {
                $lastMsg = Message::where('conversation_id', $c->id)
                    ->orderByDesc('created_at')
                    ->first();

                return $lastMsg?->created_at;
            });

        $userIds = $conversations->map(function ($conv) use ($userId) {
            $other = $conv->otherUser(User::find($userId));

            return $other?->id;
        })->filter()->unique()->values()->toArray();
        $userIds[] = $userId;

        $profiles = Profile::whereIn('user_id', $userIds)->get()->keyBy('user_id');
        $usersWithProfiles = User::whereIn('id', $userIds)->get()->keyBy('id');

        $conversations->each(function ($conv) use ($userId, $profiles, $usersWithProfiles) {
            $currentUser = User::find($userId);
            $otherUser = $conv->otherUser($currentUser);
            if ($otherUser && isset($usersWithProfiles[$otherUser->id])) {
                $other = $usersWithProfiles[$otherUser->id];
                $other->setRelation('profile', $profiles[$otherUser->id] ?? null);
                $conv->setRelation('otherChat', $other);
            }
        });

        return $conversations;
    }
}
