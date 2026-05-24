<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaryResponseComment extends Model
{
    protected $fillable = [
        'diary_response_id',
        'user_id',
        'parent_id',
        'content',
    ];

    protected $appends = ['created_at_human'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(DiaryResponse::class, 'diary_response_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->latest();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(DiaryResponseCommentLike::class, 'diary_response_comment_id');
    }

    public function isLikedBy(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function getIsLikedAttribute(): bool
    {
        $userId = auth()->id();
        if (! $userId) {
            return false;
        }

        return $this->isLikedBy($userId);
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
