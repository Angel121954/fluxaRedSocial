<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'privacy',
        'likes_count',
        'comments_count',
        'shares_count',
        'parent_id',
    ];

    // Relación con media
    public function media()
    {
        return $this->hasMany(ProjectMedia::class)->orderBy('position');
    }

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Si es una respuesta a otro proyecto (parent_id)
    public function parent()
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    public function likes()
    {
        return $this->hasMany(ProjectLike::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(ProjectBookmark::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function skillEndorsements()
    {
        return $this->hasMany(SkillEndorsement::class);
    }

    public function scopePublicRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id')->where('privacy', 'public');
    }

    public function scopeExcludingBlocked(Builder $query, Collection $blockedIds): Builder
    {
        return $query->when(
            $blockedIds->isNotEmpty(),
            fn (Builder $query) => $query->whereNotIn('user_id', $blockedIds)
        );
    }
}
