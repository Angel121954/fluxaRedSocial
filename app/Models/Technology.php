<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    private const ICON_OVERIDES = [
        'express' => 'devicon-express-original',
        'reactnative' => 'devicon-reactnative-original',
        'tensorflow' => 'devicon-tensorflow-original',
    ];

    public function deviconClass(): string
    {
        return self::ICON_OVERIDES[$this->slug] ?? 'devicon-' . $this->slug . '-plain';
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_technology');
    }
}
