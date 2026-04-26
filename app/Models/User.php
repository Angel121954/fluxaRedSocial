<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'status',
        'username',
        'password',
        'onboarding_completed',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'onboarding_completed' => 'boolean',
    ];

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
        return $this->belongsToMany(Technology::class);
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
}
