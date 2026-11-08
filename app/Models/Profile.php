<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\ProfileObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ProfileObserver::class)]
class Profile extends Model
{
    use HasFactory;

    public const SPECIALTIES = [
        'frontend',
        'backend',
        'fullstack',
        'devops',
        'mobile',
        'data',
    ];

    public const SPECIALTY_LABELS = [
        'frontend' => 'Frontend',
        'backend' => 'Backend',
        'fullstack' => 'Full Stack',
        'devops' => 'DevOps',
        'mobile' => 'Mobile',
        'data' => 'Data',
    ];

    protected $fillable = [
        'user_id',
        'avatar',
        'cover_image',
        'bio',
        'specialty',
        'country',
        'city',
        'language',
        'phone_code',
        'phone_number',
        'website_url',
        'github_url',
        'twitter_url',
        'linkedin_url',
        'birth_date',
        'gender',
        'visibility',
        'last_seen_at',
        'cv_settings',
        'accept_messages',
        'show_email',
        'show_favorites',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'phone_code',
        'phone_number',
        'birth_date',
        'gender',
        'latitude',
        'longitude',
        'cv_settings',
    ];

    protected $casts = [
        'cv_settings' => 'array',
        'accept_messages' => 'boolean',
        'show_email' => 'boolean',
        'show_favorites' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function isVisibleTo(?User $viewer): bool
    {
        if ($viewer !== null && $viewer->id === $this->user_id) {
            return true;
        }

        if (! $this->user?->isVisible() || $this->user->role === 'guest') {
            return $viewer?->role === 'admin';
        }

        if ($viewer !== null && $viewer->isBlockedWithId($this->user_id)) {
            return false;
        }

        return match ($this->visibility) {
            'public' => true,
            'followers' => $viewer !== null
                && $viewer->follows()->where('followed_id', $this->user_id)->exists(),
            default => false,
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
