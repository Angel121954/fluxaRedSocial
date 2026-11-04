<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'privacy',
        'likes_count',
        'comments_count',
        'shares_count',
        'parent_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            if (blank($project->slug)) {
                $project->slug = static::uniqueSlug($project->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Permite que enlaces antiguos con ID sigan funcionando.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first()
            ?? $this->whereKey($value)->first();
    }

    private static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'proyecto';
        $slug = $base;
        $suffix = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    public function getCoverUrlAttribute(): ?string
    {
        $media = $this->media->firstWhere('type', 'image');

        if (! $media) {
            return null;
        }

        if (! $media->public_id) {
            return $media->media_url;
        }

        $cloud = config('cloudinary.cloud_name');

        return "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,g_auto,q_auto:good,f_auto,w_1200,h_630/{$media->public_id}";
    }

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

    /**
     * Proyectos que pueden aparecer en superficies de descubrimiento.
     */
    public function scopeDiscoverable(Builder $query): Builder
    {
        return $query->publicRoot()
            ->whereHas('user', function (Builder $query): void {
                $query->where('role', '!=', 'guest')
                    ->where('status', '!=', 'banned');
            });
    }

    public function scopeExcludingBlocked(Builder $query, Collection $blockedIds): Builder
    {
        return $query->when(
            $blockedIds->isNotEmpty(),
            fn (Builder $query) => $query->whereNotIn('user_id', $blockedIds)
        );
    }
}
