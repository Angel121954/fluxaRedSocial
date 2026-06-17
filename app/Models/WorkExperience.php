<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'company',
        'position',
        'location',
        'started_at',
        'ended_at',
        'current',
        'description',
    ];

    protected $casts = [
        'started_at' => 'date:Y-m-d',
        'ended_at' => 'date:Y-m-d',
        'current' => 'boolean',
    ];

    public const TYPES = [
        'formal' => 'Empleo formal',
        'freelance' => 'Freelance / Cliente',
        'personal' => 'Proyecto personal',
        'volunteering' => 'Voluntariado',
    ];

    // Relación con User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
