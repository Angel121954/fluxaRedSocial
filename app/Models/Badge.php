<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'criteria_type',
        'criteria_config',
        'tier',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'criteria_config' => 'array',
            'tier' => 'integer',
            'order' => 'integer',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badge')
            ->withPivot('earned_at', 'notified')
            ->withTimestamps();
    }
}
