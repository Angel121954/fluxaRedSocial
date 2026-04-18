<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
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

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function bookmarks()
    {
        return $this->hasMany(ProjectBookmark::class);
    }

    public function skillEndorsements()
    {
        return $this->hasMany(SkillEndorsement::class);
    }

    public function getSkillCountsAttribute(): array
    {
        return SkillEndorsement::getSkillCounts($this->id);
    }

    public function getUserEndorsementAttribute($userId): ?string
    {
        return SkillEndorsement::getUserEndorsement($userId, $this->id);
    }

    public function isBookmarkedBy($userId)
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    // Accessor — lo llamas como $project->days_active
    public function getDaysActiveAttribute(): int
    {
        return (int) $this->created_at->diffInDays(now());
    }
}
