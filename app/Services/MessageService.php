<?php

namespace App\Services;

use App\Events\ConversationCreated;
use App\Events\NewMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Support\Collection;
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
        if ($this->isBlockedBy($senderId, $recipientId)) {
            return false;
        }

        $recipientProfile = Profile::where('user_id', $recipientId)->first();

        return $recipientProfile && $recipientProfile->accept_messages;
    }

    public function isBlockedBy(int $userId, int $otherUserId): bool
    {
        return UserBlock::where('blocker_id', $otherUserId)
            ->where('blocked_id', $userId)
            ->exists();
    }

    public function hasBlocked(int $blockerId, int $blockedId): bool
    {
        return UserBlock::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }

    public function blockUser(int $blockerId, int $blockedId): void
    {
        UserBlock::firstOrCreate([
            'blocker_id' => $blockerId,
            'blocked_id' => $blockedId,
        ]);
    }

    public function unblockUser(int $blockerId, int $blockedId): void
    {
        UserBlock::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->delete();
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
            Log::error('Broadcast failed: '.$e->getMessage());
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

    public function getUserConversations(int $userId, ?int $activeConversationId = null): Collection
    {
        $currentUser = User::find($userId);

        $conversations = Conversation::with(['userA', 'userB', 'latestMessage'])
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('sender_id', '!=', $userId)
                    ->whereNull('read_at');
            }])
            ->where(function ($q) use ($userId) {
                $q->where('user_a_id', $userId)->orWhere('user_b_id', $userId);
            })
            ->orderByDesc(Message::select('created_at')
                ->whereColumn('conversation_id', 'conversations.id')
                ->latest()
                ->take(1))
            ->get();

        $userIds = $conversations->map(function ($conv) use ($currentUser) {
            $other = $conv->otherUser($currentUser);

            return $other?->id;
        })->filter()->unique()->values()->toArray();
        $userIds[] = $currentUser->id;

        $profiles = Profile::whereIn('user_id', $userIds)->get()->keyBy('user_id');
        $usersWithProfiles = User::whereIn('id', $userIds)->get()->keyBy('id');

        $conversations->each(function ($conv) use ($currentUser, $profiles, $usersWithProfiles) {
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
