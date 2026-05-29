<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Technology;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            ['name' => 'JavaScript',   'slug' => 'javascript',    'icon' => 'javascript.png', 'category' => 'language',  'website_url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript'],
            ['name' => 'TypeScript',   'slug' => 'typescript',    'icon' => 'typescript.png', 'category' => 'language',  'website_url' => 'https://www.typescriptlang.org'],
            ['name' => 'Python',       'slug' => 'python',        'icon' => 'python.png',     'category' => 'language',  'website_url' => 'https://www.python.org'],
            ['name' => 'PHP',          'slug' => 'php',           'icon' => 'php.png',        'category' => 'language',  'website_url' => 'https://www.php.net'],
            ['name' => 'Java',         'slug' => 'java',          'icon' => 'java.png',       'category' => 'language',  'website_url' => 'https://www.java.com'],
            ['name' => 'C#',           'slug' => 'csharp',        'icon' => 'csharp.png',     'category' => 'language',  'website_url' => 'https://learn.microsoft.com/en-us/dotnet/csharp/'],
            ['name' => 'Go',           'slug' => 'go',            'icon' => 'go.png',         'category' => 'language',  'website_url' => 'https://go.dev'],
            ['name' => 'Rust',         'slug' => 'rust',          'icon' => 'rust.png',       'category' => 'language',  'website_url' => 'https://www.rust-lang.org'],
            ['name' => 'Swift',        'slug' => 'swift',         'icon' => 'swift.png',      'category' => 'language',  'website_url' => 'https://www.swift.org'],
            ['name' => 'Kotlin',       'slug' => 'kotlin',        'icon' => 'kotlin.png',     'category' => 'language',  'website_url' => 'https://kotlinlang.org'],
            ['name' => 'C++',          'slug' => 'cplusplus',                                'category' => 'language',  'website_url' => 'https://isocpp.org'],
            ['name' => 'Ruby',         'slug' => 'ruby',                                     'category' => 'language',  'website_url' => 'https://www.ruby-lang.org'],
            ['name' => 'Dart',         'slug' => 'dart',                                     'category' => 'language',  'website_url' => 'https://dart.dev'],
            ['name' => 'Elixir',       'slug' => 'elixir',                                   'category' => 'language',  'website_url' => 'https://elixir-lang.org'],
            ['name' => 'React',        'slug' => 'react',         'icon' => 'react.png',      'category' => 'framework', 'website_url' => 'https://react.dev'],
            ['name' => 'Next.js',      'slug' => 'nextjs',        'icon' => 'nextjs.png',     'category' => 'framework', 'website_url' => 'https://nextjs.org'],
            ['name' => 'Vue',          'slug' => 'vuejs',         'icon' => 'vuejs.png',      'category' => 'framework', 'website_url' => 'https://vuejs.org'],
            ['name' => 'Angular',      'slug' => 'angularjs',     'icon' => 'angularjs.png',  'category' => 'framework', 'website_url' => 'https://angular.dev'],
            ['name' => 'Svelte',       'slug' => 'svelte',        'icon' => 'svelte.png',     'category' => 'framework', 'website_url' => 'https://svelte.dev'],
            ['name' => 'Express.js',   'slug' => 'express',       'icon' => 'express.png',    'category' => 'framework', 'website_url' => 'https://expressjs.com'],
            ['name' => 'Laravel',      'slug' => 'laravel',       'icon' => 'laravel.png',    'category' => 'framework', 'website_url' => 'https://laravel.com'],
            ['name' => 'Django',       'slug' => 'django',        'icon' => 'django.png',     'category' => 'framework', 'website_url' => 'https://www.djangoproject.com'],
            ['name' => 'Spring Boot',  'slug' => 'spring',        'icon' => 'spring.png',     'category' => 'framework', 'website_url' => 'https://spring.io/projects/spring-boot'],
            ['name' => 'Ruby on Rails','slug' => 'rails',         'icon' => 'rails.png',      'category' => 'framework', 'website_url' => 'https://rubyonrails.org'],
            ['name' => 'Nuxt.js',      'slug' => 'nuxtjs',        'icon' => 'nuxtjs.png',     'category' => 'framework', 'website_url' => 'https://nuxt.com'],
            ['name' => 'React Native', 'slug' => 'reactnative',   'icon' => 'reactnative.png','category' => 'framework', 'website_url' => 'https://reactnative.dev'],
            ['name' => 'Flutter',      'slug' => 'flutter',       'icon' => 'flutter.png',    'category' => 'framework', 'website_url' => 'https://flutter.dev'],
            ['name' => 'NestJS',       'slug' => 'nestjs',                                   'category' => 'framework', 'website_url' => 'https://nestjs.com'],
            ['name' => 'Flask',        'slug' => 'flask',                                    'category' => 'framework', 'website_url' => 'https://flask.palletsprojects.com'],
            ['name' => 'FastAPI',      'slug' => 'fastapi',                                  'category' => 'framework', 'website_url' => 'https://fastapi.tiangolo.com'],
            ['name' => 'ASP.NET',      'slug' => 'dotnetcore',                               'category' => 'framework', 'website_url' => 'https://dotnet.microsoft.com/en-us/apps/aspnet'],
            ['name' => 'Symfony',      'slug' => 'symfony',                                  'category' => 'framework', 'website_url' => 'https://symfony.com'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwindcss',   'icon' => 'tailwindcss.png','category' => 'library',   'website_url' => 'https://tailwindcss.com'],
            ['name' => 'Node.js',      'slug' => 'nodejs',        'icon' => 'nodejs.png',     'category' => 'library',   'website_url' => 'https://nodejs.org'],
            ['name' => 'Bootstrap',    'slug' => 'bootstrap',     'icon' => 'bootstrap.png',  'category' => 'library',   'website_url' => 'https://getbootstrap.com'],
            ['name' => 'GraphQL',      'slug' => 'graphql',       'icon' => 'graphql.png',    'category' => 'library',   'website_url' => 'https://graphql.org'],
            ['name' => 'TensorFlow',   'slug' => 'tensorflow',    'icon' => 'tensorflow.png', 'category' => 'library',   'website_url' => 'https://www.tensorflow.org'],
            ['name' => 'Livewire',     'slug' => 'livewire',                                 'category' => 'library',   'website_url' => 'https://livewire.laravel.com'],
            ['name' => 'MySQL',        'slug' => 'mysql',         'icon' => 'mysql.png',      'category' => 'database',  'website_url' => 'https://www.mysql.com'],
            ['name' => 'PostgreSQL',   'slug' => 'postgresql',    'icon' => 'postgresql.png', 'category' => 'database',  'website_url' => 'https://www.postgresql.org'],
            ['name' => 'MongoDB',      'slug' => 'mongodb',       'icon' => 'mongodb.png',    'category' => 'database',  'website_url' => 'https://www.mongodb.com'],
            ['name' => 'Redis',        'slug' => 'redis',         'icon' => 'redis.png',      'category' => 'database',  'website_url' => 'https://redis.io'],
            ['name' => 'SQLite',       'slug' => 'sqlite',                                   'category' => 'database',  'website_url' => 'https://www.sqlite.org'],
            ['name' => 'MariaDB',      'slug' => 'mariadb',                                  'category' => 'database',  'website_url' => 'https://mariadb.org'],
            ['name' => 'Docker',       'slug' => 'docker',        'icon' => 'docker.png',     'category' => 'tool',      'website_url' => 'https://www.docker.com'],
            ['name' => 'Kubernetes',   'slug' => 'kubernetes',    'icon' => 'kubernetes.png', 'category' => 'tool',      'website_url' => 'https://kubernetes.io'],
            ['name' => 'Git',          'slug' => 'git',           'icon' => 'git.png',        'category' => 'tool',      'website_url' => 'https://git-scm.com'],
            ['name' => 'Linux',        'slug' => 'linux',         'icon' => 'linux.png',      'category' => 'tool',      'website_url' => 'https://www.linux.org'],
            ['name' => 'Figma',        'slug' => 'figma',         'icon' => 'figma.png',      'category' => 'tool',      'website_url' => 'https://www.figma.com'],
            ['name' => 'GitHub Actions','slug' => 'githubactions',                           'category' => 'tool',      'website_url' => 'https://github.com/features/actions'],
            ['name' => 'Postman',      'slug' => 'postman',                                  'category' => 'tool',      'website_url' => 'https://www.postman.com'],
            ['name' => 'Vite',         'slug' => 'vite',                                     'category' => 'tool',      'website_url' => 'https://vite.dev'],
            ['name' => 'PyTorch',      'slug' => 'pytorch',                                  'category' => 'library',   'website_url' => 'https://pytorch.org'],
            ['name' => 'AWS',          'slug' => 'amazonwebservices', 'icon' => 'amazonwebservices.png', 'category' => 'platform', 'website_url' => 'https://aws.amazon.com'],
            ['name' => 'Firebase',     'slug' => 'firebase',      'icon' => 'firebase.png',   'category' => 'platform', 'website_url' => 'https://firebase.google.com'],
            ['name' => 'Supabase',     'slug' => 'supabase',                                 'category' => 'platform', 'website_url' => 'https://supabase.com'],
            ['name' => 'Google Cloud', 'slug' => 'googlecloud',                              'category' => 'platform', 'website_url' => 'https://cloud.google.com'],
            ['name' => 'Azure',        'slug' => 'azure',                                    'category' => 'platform', 'website_url' => 'https://azure.microsoft.com'],
            ['name' => 'Vercel',       'slug' => 'vercel',                                   'category' => 'platform', 'website_url' => 'https://vercel.com'],
            ['name' => 'Netlify',      'slug' => 'netlify',                                  'category' => 'platform', 'website_url' => 'https://www.netlify.com'],
        ];

        foreach ($technologies as $tech) {
            Technology::updateOrCreate(['slug' => $tech['slug']], $tech);
        }
    }
}
