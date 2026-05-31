<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'category', 'website_url'];

    protected $casts = [
        'category' => 'string',
    ];

    private const ICON_OVERIDES = [
        'express' => 'devicon-express-original',
        'reactnative' => 'devicon-reactnative-original',
        'tensorflow' => 'devicon-tensorflow-original',
        'electron' => 'devicon-electron-original',
        'threejs' => 'devicon-threejs-original',
        'ionic' => 'devicon-ionic-original',
        'mongoose' => 'devicon-mongoose-original',
        'less' => 'devicon-less-plain-wordmark',
        'algolia' => 'devicon-algolia-original',
    ];

    private const SVG_TYPE_OVERRIDES = [
        'amazonwebservices' => 'plain-wordmark',
        'angularjs' => 'plain',
        'django' => 'plain',
        'tailwindcss' => 'original',
        'kubernetes' => 'plain',
        'graphql' => 'plain',
        'firebase' => 'plain',
        'express' => 'original-wordmark',
        'dotnetcore' => 'plain',
        'dotnetmaui' => 'plain',
        'less' => 'plain-wordmark',
        'objectivec' => 'plain',
        'jest' => 'plain',
        'rails' => 'plain',
        'codeigniter' => 'plain',
    ];

    public function deviconClass(): string
    {
        return self::ICON_OVERIDES[$this->slug] ?? 'devicon-'.$this->slug.'-plain';
    }

    public function iconUrl(): string
    {
        $slug = (string) $this->slug;
        $type = self::SVG_TYPE_OVERRIDES[$slug] ?? 'original';

        return "https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{$slug}/{$slug}-{$type}.svg";
    }

    public function initials(): string
    {
        return strtoupper(substr((string) $this->name, 0, 2));
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
