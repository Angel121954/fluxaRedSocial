<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_a_id',
        'user_b_id',
    ];

    public function userA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_a_id');
    }

    public function userB(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_b_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function otherUser(User $user): ?User
    {
        if ($this->user_a_id === $user->id) {
            return $this->userB;
        }
        if ($this->user_b_id === $user->id) {
            return $this->userA;
        }

        return null;
    }

    public function otherUserId(int $userId): ?User
    {
        if ($this->user_a_id === $userId) {
            return $this->userB;
        }
        if ($this->user_b_id === $userId) {
            return $this->userA;
        }

        return null;
    }

    public function lastMessage(): ?Message
    {
        return $this->messages()->latest()->first();
    }

    public function unread(?int $userId = null): bool
    {
        return $this->unreadCount($userId) > 0;
    }

    public function unreadCount(?int $userId = null): int
    {
        $userId = $userId ?? auth()->id();

        if (! $this->relationLoaded('messages')) {
            return Message::where('conversation_id', $this->id)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->count();
        }

        return $this->messages
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public static function getUnreadGlobalCount(int $userId, ?int $excludeConvId = null): int
    {
        $conversationIds = self::where('user_a_id', $userId)
            ->orWhere('user_b_id', $userId)
            ->pluck('id');

        $query = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at');

        if ($excludeConvId) {
            $query->where('conversation_id', '!=', $excludeConvId);
        }

        return $query->count();
    }
}
