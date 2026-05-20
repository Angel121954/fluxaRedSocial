<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaryResponse extends Model
{
    protected $fillable = [
        'diary_id',
        'user_id',
        'content',
    ];

    public function diary(): BelongsTo
    {
        return $this->belongsTo(Diary::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(DiaryResponseLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DiaryResponseComment::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(DiaryResponseBookmark::class);
    }

    public function getLikedByAuthAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    public function getBookmarkedByAuthAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->bookmarks()->where('user_id', auth()->id())->exists();
    }
}
