<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'name',
        'avatar',
        'cover_image',
        'bio',
        'location',
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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'cv_settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
