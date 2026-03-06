<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Technology;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            ['name' => 'Laravel',    'slug' => 'laravel',     'icon' => 'laravel.png'],
            ['name' => 'React',      'slug' => 'react',       'icon' => 'react.png'],
            ['name' => 'Vue',        'slug' => 'vue',         'icon' => 'vue.png'],
            ['name' => 'Python',     'slug' => 'python',      'icon' => 'python.png'],
            ['name' => 'Node.js',    'slug' => 'nodejs',      'icon' => 'nodejs.png'],
            ['name' => 'Docker',     'slug' => 'docker',      'icon' => 'docker.png'],
            ['name' => 'TypeScript', 'slug' => 'typescript',  'icon' => 'typescript.png'],
            ['name' => 'Flutter',    'slug' => 'flutter',     'icon' => 'flutter.png'],
            ['name' => 'AWS',        'slug' => 'aws',         'icon' => 'aws.png'],
            ['name' => 'MySQL',      'slug' => 'mysql',       'icon' => 'mysql.png'],
            ['name' => 'MongoDB',    'slug' => 'mongodb',     'icon' => 'mongodb.png'],
            ['name' => 'Figma',      'slug' => 'figma',       'icon' => 'figma.png'],
            ['name' => 'Angular',    'slug' => 'angular',     'icon' => 'angular.png'],
            ['name' => 'Django',     'slug' => 'django',      'icon' => 'django.png'],
            ['name' => 'Rust',       'slug' => 'rust',        'icon' => 'rust.png'],
            ['name' => 'Kotlin',     'slug' => 'kotlin',      'icon' => 'kotlin.png'],
            ['name' => 'Swift',      'slug' => 'swift',       'icon' => 'swift.png'],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql',  'icon' => 'postgresql.png'],
            ['name' => 'Redis',      'slug' => 'redis',       'icon' => 'redis.png'],
            ['name' => 'GraphQL',    'slug' => 'graphql',     'icon' => 'graphql.png'],
        ];

        foreach ($technologies as $tech) {
            Technology::firstOrCreate(['slug' => $tech['slug']], $tech);
        }
    }
}
