<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMedia extends Model
{
    protected $table = 'project_media';

    protected $fillable = [
        'project_id',
        'media_url',
        'type',
        'position',
        'public_id',
    ];

    // Publicación a la que pertenece
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
