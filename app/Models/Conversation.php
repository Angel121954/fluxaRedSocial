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
        'deleted_by_a_at',
        'deleted_by_b_at'
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
        return Message::whereIn('conversation_id', function ($q) {
            $q->select('id')
                ->from('conversations')
                ->where('user_a_id', auth()->id())
                ->orWhere('user_b_id', auth()->id());
        })
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();
    }
}
