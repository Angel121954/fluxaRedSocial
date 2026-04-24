<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'link',
        'from_user_id',
        'reference_id',
        'reference_type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    const TYPE_MESSAGE = 'message';
    const TYPE_FOLLOW = 'follow';
    const TYPE_LIKE = 'like';
    const TYPE_COMMENT = 'comment';
    const TYPE_MENTION = 'mention';
    const TYPE_PROJECT = 'project';
    const TYPE_ENDORSEMENT = 'endorsement';

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'message' => 'Mensaje',
            'follow' => 'Nuevo seguidor',
            'like' => 'Like',
            'comment' => 'Comentario',
            'mention' => 'Mención',
            'project' => 'Proyecto',
            'endorsement' => 'Recomendación',
            default => 'Notificación',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function unreadCount(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}