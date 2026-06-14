<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'role',
        'status',
        'username',
        'password',
        'provider',
        'provider_id',
        'onboarding_completed',
        'account_type',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'password' => 'hashed',
        'onboarding_completed' => 'boolean',
        'github_token_expires_at' => 'datetime',
    ];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function notificationPreferences()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class)->withPivot('is_favorite');
    }

    public function favoriteTechnologies()
    {
        return $this->belongsToMany(Technology::class)
            ->withPivot('is_favorite')
            ->wherePivot('is_favorite', true);
    }

    public function toggleFavoriteTechnology(int $technologyId): bool
    {
        $pivot = $this->technologies()->where('technology_id', $technologyId)->first();

        if (! $pivot) {
            return false;
        }

        $current = (bool) $pivot->pivot->is_favorite;

        $this->technologies()->updateExistingPivot($technologyId, [
            'is_favorite' => ! $current,
        ]);

        return ! $current;
    }

    public function bookmarkedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_bookmarks')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class)->orderBy('started_at', 'desc');
    }

    public function educations()
    {
        return $this->hasMany(Education::class)->orderBy('graduated_year', 'desc');
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
    }

    public function suggestions()
    {
        return $this->hasOne(Suggestion::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badge')
            ->withPivot('earned_at', 'notified')
            ->withTimestamps();
    }

    public function salaryReports()
    {
        return $this->hasMany(SalaryReport::class);
    }

    public function bookmarkedJobs()
    {
        return $this->belongsToMany(Job::class, 'job_bookmarks')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function skillEndorsementsReceived()
    {
        return $this->hasMany(SkillEndorsement::class, 'user_id');
    }

    public function getSkillCounts(): array
    {
        $counts = $this->skillEndorsementsReceived()
            ->select('skill_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('skill_type')
            ->pluck('count', 'skill_type')
            ->toArray();

        $result = [];
        foreach (SkillEndorsement::SKILLS as $key => $skill) {
            $result[$key] = $counts[$key] ?? 0;
        }

        return $result;
    }

    public function getMasteryLevel(string $skillType): string
    {
        $count = $this->skillEndorsementsReceived()
            ->where('skill_type', $skillType)
            ->count();

        return match (true) {
            $count >= 500 => 'master',
            $count >= 200 => 'advanced',
            $count >= 50 => 'competent',
            default => 'novice',
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile?->avatar) {
            return $this->profile->avatar;
        }

        $seed = strtolower($this->username ?? $this->name ?? 'user');

        return "https://api.dicebear.com/7.x/initials/svg?seed={$seed}&backgroundColor=12b3b6";
    }

    public function getIsBannedAttribute(): bool
    {
        return $this->status === 'banned';
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocker_id', 'blocked_id')
            ->withTimestamps();
    }

    public function blockedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_id', 'blocker_id')
            ->withTimestamps();
    }

    public function hasBlock(User $user): bool
    {
        return $this->blockedUsers()->where('blocked_id', $user->id)->exists();
    }

    public function hasBadge(string $slug): bool
    {
        if ($this->relationLoaded('badges')) {
            return $this->badges->contains('slug', $slug);
        }

        return $this->badges()->where('slug', $slug)->exists();
    }
}
