<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Technology;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            ['name' => 'JavaScript',   'slug' => 'javascript',    'icon' => 'javascript.png'],
            ['name' => 'TypeScript',   'slug' => 'typescript',    'icon' => 'typescript.png'],
            ['name' => 'Python',       'slug' => 'python',        'icon' => 'python.png'],
            ['name' => 'PHP',          'slug' => 'php',           'icon' => 'php.png'],
            ['name' => 'Java',         'slug' => 'java',          'icon' => 'java.png'],
            ['name' => 'C#',           'slug' => 'csharp',        'icon' => 'csharp.png'],
            ['name' => 'Go',           'slug' => 'go',            'icon' => 'go.png'],
            ['name' => 'Rust',         'slug' => 'rust',          'icon' => 'rust.png'],
            ['name' => 'React',        'slug' => 'react',         'icon' => 'react.png'],
            ['name' => 'Next.js',      'slug' => 'nextjs',        'icon' => 'nextjs.png'],
            ['name' => 'Vue',          'slug' => 'vuejs',         'icon' => 'vuejs.png'],
            ['name' => 'Angular',      'slug' => 'angularjs',     'icon' => 'angularjs.png'],
            ['name' => 'Svelte',       'slug' => 'svelte',        'icon' => 'svelte.png'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwindcss',   'icon' => 'tailwindcss.png'],
            ['name' => 'Node.js',      'slug' => 'nodejs',        'icon' => 'nodejs.png'],
            ['name' => 'Express.js',   'slug' => 'express',       'icon' => 'express.png'],
            ['name' => 'Laravel',      'slug' => 'laravel',       'icon' => 'laravel.png'],
            ['name' => 'Django',       'slug' => 'django',        'icon' => 'django.png'],
            ['name' => 'Spring Boot',  'slug' => 'spring',        'icon' => 'spring.png'],
            ['name' => 'Ruby on Rails','slug' => 'rails',   'icon' => 'rails.png'],
            ['name' => 'Nuxt.js',      'slug' => 'nuxtjs',        'icon' => 'nuxtjs.png'],
            ['name' => 'Bootstrap',    'slug' => 'bootstrap',     'icon' => 'bootstrap.png'],
            ['name' => 'React Native', 'slug' => 'reactnative',   'icon' => 'reactnative.png'],
            ['name' => 'Flutter',      'slug' => 'flutter',       'icon' => 'flutter.png'],
            ['name' => 'Swift',        'slug' => 'swift',         'icon' => 'swift.png'],
            ['name' => 'Kotlin',       'slug' => 'kotlin',        'icon' => 'kotlin.png'],
            ['name' => 'MySQL',        'slug' => 'mysql',         'icon' => 'mysql.png'],
            ['name' => 'PostgreSQL',   'slug' => 'postgresql',    'icon' => 'postgresql.png'],
            ['name' => 'MongoDB',      'slug' => 'mongodb',       'icon' => 'mongodb.png'],
            ['name' => 'Redis',        'slug' => 'redis',         'icon' => 'redis.png'],
            ['name' => 'Firebase',     'slug' => 'firebase',      'icon' => 'firebase.png'],
            ['name' => 'AWS',          'slug' => 'amazonwebservices', 'icon' => 'amazonwebservices.png'],
            ['name' => 'Docker',       'slug' => 'docker',        'icon' => 'docker.png'],
            ['name' => 'Kubernetes',   'slug' => 'kubernetes',    'icon' => 'kubernetes.png'],
            ['name' => 'Git',          'slug' => 'git',           'icon' => 'git.png'],
            ['name' => 'Linux',        'slug' => 'linux',         'icon' => 'linux.png'],
            ['name' => 'Figma',        'slug' => 'figma',         'icon' => 'figma.png'],
            ['name' => 'GraphQL',      'slug' => 'graphql',       'icon' => 'graphql.png'],
            ['name' => 'TensorFlow',   'slug' => 'tensorflow',    'icon' => 'tensorflow.png'],
        ];

        foreach ($technologies as $tech) {
            Technology::firstOrCreate(['slug' => $tech['slug']], $tech);
        }
    }
}
