<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Technology;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            // Lenguajes de programación
            ['name' => 'JavaScript',   'slug' => 'javascript',    'icon' => 'javascript.png'],
            ['name' => 'TypeScript',   'slug' => 'typescript',    'icon' => 'typescript.png'],
            ['name' => 'Python',       'slug' => 'python',        'icon' => 'python.png'],
            ['name' => 'PHP',          'slug' => 'php',           'icon' => 'php.png'],
            ['name' => 'Java',         'slug' => 'java',          'icon' => 'java.png'],
            ['name' => 'C#',           'slug' => 'csharp',        'icon' => 'csharp.png'],
            ['name' => 'C++',          'slug' => 'cplusplus',     'icon' => 'cplusplus.png'],
            ['name' => 'Express.js',   'slug' => 'express',       'icon' => 'express.png'],
            ['name' => 'ASP.NET',      'slug' => 'dotnetcore',    'icon' => 'dotnetcore.png'],
            ['name' => 'Spring Boot',  'slug' => 'spring',        'icon' => 'spring.png'],
            ['name' => 'ASP.NET',      'slug' => 'aspnet',        'icon' => 'aspnet.png'],
            ['name' => 'Ruby on Rails','slug' => 'rubyonrails',   'icon' => 'rubyonrails.png'],
            ['name' => 'Symfony',      'slug' => 'symfony',       'icon' => 'symfony.png'],

            // Frameworks frontend
            ['name' => 'React',        'slug' => 'react',         'icon' => 'react.png'],
            ['name' => 'Next.js',      'slug' => 'nextjs',        'icon' => 'nextjs.png'],
            ['name' => 'Vue',          'slug' => 'vuejs',         'icon' => 'vuejs.png'],
            ['name' => 'Nuxt.js',      'slug' => 'nuxtjs',        'icon' => 'nuxtjs.png'],
            ['name' => 'Angular',      'slug' => 'angularjs',     'icon' => 'angularjs.png'],
            ['name' => 'Svelte',       'slug' => 'svelte',        'icon' => 'svelte.png'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwindcss',   'icon' => 'tailwindcss.png'],
            ['name' => 'Bootstrap',    'slug' => 'bootstrap',     'icon' => 'bootstrap.png'],
            ['name' => 'Livewire',     'slug' => 'livewire',      'icon' => 'livewire.png'],

            // Mobile
            ['name' => 'React Native', 'slug' => 'reactnative',   'icon' => 'reactnative.png'],
            ['name' => 'Flutter',      'slug' => 'flutter',       'icon' => 'flutter.png'],

            // Bases de datos
            ['name' => 'MySQL',        'slug' => 'mysql',         'icon' => 'mysql.png'],
            ['name' => 'PostgreSQL',   'slug' => 'postgresql',    'icon' => 'postgresql.png'],
            ['name' => 'MongoDB',      'slug' => 'mongodb',       'icon' => 'mongodb.png'],
            ['name' => 'SQLite',       'slug' => 'sqlite',        'icon' => 'sqlite.png'],
            ['name' => 'MariaDB',      'slug' => 'mariadb',       'icon' => 'mariadb.png'],
            ['name' => 'Redis',        'slug' => 'redis',         'icon' => 'redis.png'],
            ['name' => 'Firebase',     'slug' => 'firebase',      'icon' => 'firebase.png'],
            ['name' => 'Supabase',     'slug' => 'supabase',      'icon' => 'supabase.png'],

            // Cloud & DevOps
            ['name' => 'AWS',          'slug' => 'amazonwebservices', 'icon' => 'amazonwebservices.png'],
            ['name' => 'Google Cloud', 'slug' => 'googlecloud',   'icon' => 'googlecloud.png'],
            ['name' => 'Azure',        'slug' => 'azure',         'icon' => 'azure.png'],
            ['name' => 'Docker',       'slug' => 'docker',        'icon' => 'docker.png'],
            ['name' => 'Kubernetes',   'slug' => 'kubernetes',    'icon' => 'kubernetes.png'],
            ['name' => 'Linux',        'slug' => 'linux',         'icon' => 'linux.png'],
            ['name' => 'Git',          'slug' => 'git',           'icon' => 'git.png'],
            ['name' => 'GitHub Actions','slug' => 'githubactions','icon' => 'githubactions.png'],
            ['name' => 'Vercel',       'slug' => 'vercel',        'icon' => 'vercel.png'],
            ['name' => 'Netlify',      'slug' => 'netlify',       'icon' => 'netlify.png'],

            // Herramientas
            ['name' => 'Figma',        'slug' => 'figma',         'icon' => 'figma.png'],
            ['name' => 'GraphQL',      'slug' => 'graphql',       'icon' => 'graphql.png'],
            ['name' => 'Postman',      'slug' => 'postman',       'icon' => 'postman.png'],
            ['name' => 'Vite',         'slug' => 'vite',          'icon' => 'vite.png'],

            // IA / Data
            ['name' => 'TensorFlow',   'slug' => 'tensorflow',    'icon' => 'tensorflow.png'],
            ['name' => 'PyTorch',      'slug' => 'pytorch',       'icon' => 'pytorch.png'],
        ];

        foreach ($technologies as $tech) {
            Technology::firstOrCreate(['slug' => $tech['slug']], $tech);
        }
    }
}
