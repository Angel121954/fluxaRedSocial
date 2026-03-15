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
    // Accessor — lo llamas como $project->days_active
    public function getDaysActiveAttribute(): int
    {
        return (int) $this->created_at->diffInDays(now());
    }
}
