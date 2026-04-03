<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectBookmark extends Model
{
    protected $table = 'project_bookmarks';

    protected $fillable = [
        'user_id',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
