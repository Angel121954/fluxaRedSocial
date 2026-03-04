<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_enabled',
        'push_enabled',
        'notify_comments',
        'notify_followers',
        'notify_mentions',
        'weekly_summary',
    ];

    protected $casts = [
        'email_enabled'    => 'boolean',
        'push_enabled'     => 'boolean',
        'notify_comments'  => 'boolean',
        'notify_followers' => 'boolean',
        'notify_mentions'  => 'boolean',
        'weekly_summary'   => 'boolean',
    ];

    // Relación inversa hacia User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
