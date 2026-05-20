<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Diary extends Model
{
    protected $fillable = [
        'question',
        'emoji',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function responses(): HasMany
    {
        return $this->hasMany(DiaryResponse::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getContentAttribute(): string
    {
        return $this->question;
    }

    public function getClosesAtAttribute()
    {
        return $this->created_at->endOfDay();
    }

    public function getQuestionNumberAttribute(): int
    {
        return $this->id;
    }
}
