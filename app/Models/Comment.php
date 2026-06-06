<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'parent_id',
        'content',
    ];

    protected $appends = ['created_at_human'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function getIsLikedAttribute()
    {
        $userId = auth()->id();
        if (!$userId) return false;
        return $this->isLikedBy($userId);
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
