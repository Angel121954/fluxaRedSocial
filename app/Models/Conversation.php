<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return Message::where('conversation_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function unread(): bool
    {
        $lastMsg = $this->lastMessage();
        if (!$lastMsg) return false;
        return $lastMsg->sender_id !== auth()->id() && $lastMsg->read_at === null;
    }

    public function unreadCount(): int
    {
        return Message::where('conversation_id', $this->id)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    public function totalCount(): int
    {
        return self::getUnreadGlobalCount();
    }

    public static function getUnreadGlobalCount(): int
    {
        $conversationIds = self::where('user_a_id', auth()->id())
            ->orWhere('user_b_id', auth()->id())
            ->pluck('id');

        return Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();
    }
}
