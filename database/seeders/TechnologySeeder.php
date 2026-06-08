<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            // ═══════════════════════════════════════════════
            // LENGUAJES
            // ═══════════════════════════════════════════════
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
            ['name' => 'Scala',        'slug' => 'scala',                                    'category' => 'language',  'website_url' => 'https://scala-lang.org'],
            ['name' => 'Zig',          'slug' => 'zig',                                      'category' => 'language',  'website_url' => 'https://ziglang.org'],
            ['name' => 'Lua',          'slug' => 'lua',                                      'category' => 'language',  'website_url' => 'https://www.lua.org'],
            ['name' => 'Haskell',      'slug' => 'haskell',                                  'category' => 'language',  'website_url' => 'https://www.haskell.org'],
            ['name' => 'R',            'slug' => 'r',                                        'category' => 'language',  'website_url' => 'https://www.r-project.org'],
            ['name' => 'Perl',         'slug' => 'perl',                                     'category' => 'language',  'website_url' => 'https://www.perl.org'],
            ['name' => 'OCaml',        'slug' => 'ocaml',                                    'category' => 'language',  'website_url' => 'https://ocaml.org'],
            ['name' => 'Solidity',     'slug' => 'solidity',                                 'category' => 'language',  'website_url' => 'https://soliditylang.org'],
            ['name' => 'CSS',          'slug' => 'css3',                                     'category' => 'language',  'website_url' => 'https://developer.mozilla.org/en-US/docs/Web/CSS'],
            ['name' => 'HTML',         'slug' => 'html5',                                    'category' => 'language',  'website_url' => 'https://developer.mozilla.org/en-US/docs/Web/HTML'],
            ['name' => 'Markdown',     'slug' => 'markdown',                                 'category' => 'language',  'website_url' => 'https://www.markdownguide.org'],
            ['name' => 'Objective-C',  'slug' => 'objectivec',                               'category' => 'language',  'website_url' => 'https://developer.apple.com/documentation/objectivec'],
            ['name' => 'Clojure',      'slug' => 'clojure',                                  'category' => 'language',  'website_url' => 'https://clojure.org'],
            ['name' => 'Erlang',       'slug' => 'erlang',                                   'category' => 'language',  'website_url' => 'https://www.erlang.org'],
            ['name' => 'GraphQL',      'slug' => 'graphql',       'icon' => 'graphql.png',    'category' => 'language',  'website_url' => 'https://graphql.org'],

            // ═══════════════════════════════════════════════
            // FRAMEWORKS
            // ═══════════════════════════════════════════════
            ['name' => 'Next.js',      'slug' => 'nextjs',        'icon' => 'nextjs.png',     'category' => 'framework', 'website_url' => 'https://nextjs.org'],
            ['name' => 'Vue',          'slug' => 'vuejs',         'icon' => 'vuejs.png',      'category' => 'framework', 'website_url' => 'https://vuejs.org'],
            ['name' => 'Angular',      'slug' => 'angularjs',     'icon' => 'angularjs.png',  'category' => 'framework', 'website_url' => 'https://angular.dev'],
            ['name' => 'Svelte',       'slug' => 'svelte',        'icon' => 'svelte.png',     'category' => 'framework', 'website_url' => 'https://svelte.dev'],
            ['name' => 'Express.js',   'slug' => 'express',       'icon' => 'express.png',    'category' => 'framework', 'website_url' => 'https://expressjs.com'],
            ['name' => 'Laravel',      'slug' => 'laravel',       'icon' => 'laravel.png',    'category' => 'framework', 'website_url' => 'https://laravel.com'],
            ['name' => 'Django',       'slug' => 'django',        'icon' => 'django.png',     'category' => 'framework', 'website_url' => 'https://www.djangoproject.com'],
            ['name' => 'Spring Boot',  'slug' => 'spring',        'icon' => 'spring.png',     'category' => 'framework', 'website_url' => 'https://spring.io/projects/spring-boot'],
            ['name' => 'Ruby on Rails', 'slug' => 'rails',         'icon' => 'rails.png',      'category' => 'framework', 'website_url' => 'https://rubyonrails.org'],
            ['name' => 'Nuxt.js',      'slug' => 'nuxtjs',        'icon' => 'nuxtjs.png',     'category' => 'framework', 'website_url' => 'https://nuxt.com'],
            ['name' => 'React Native', 'slug' => 'reactnative',   'icon' => 'reactnative.png', 'category' => 'framework', 'website_url' => 'https://reactnative.dev'],
            ['name' => 'Flutter',      'slug' => 'flutter',       'icon' => 'flutter.png',    'category' => 'framework', 'website_url' => 'https://flutter.dev'],
            ['name' => 'NestJS',       'slug' => 'nestjs',                                   'category' => 'framework', 'website_url' => 'https://nestjs.com'],
            ['name' => 'Flask',        'slug' => 'flask',                                    'category' => 'framework', 'website_url' => 'https://flask.palletsprojects.com'],
            ['name' => 'FastAPI',      'slug' => 'fastapi',                                  'category' => 'framework', 'website_url' => 'https://fastapi.tiangolo.com'],
            ['name' => 'ASP.NET Core', 'slug' => 'dotnetcore',                               'category' => 'framework', 'website_url' => 'https://dotnet.microsoft.com/en-us/apps/aspnet'],
            ['name' => 'Symfony',      'slug' => 'symfony',                                  'category' => 'framework', 'website_url' => 'https://symfony.com'],
            ['name' => 'Remix',        'slug' => 'remix',                                    'category' => 'framework', 'website_url' => 'https://remix.run'],
            ['name' => 'Astro',        'slug' => 'astro',                                    'category' => 'framework', 'website_url' => 'https://astro.build'],
            ['name' => 'Gatsby',       'slug' => 'gatsby',                                   'category' => 'framework', 'website_url' => 'https://www.gatsbyjs.com'],
            ['name' => 'Tauri',        'slug' => 'tauri',                                    'category' => 'framework', 'website_url' => 'https://v2.tauri.app'],
            ['name' => 'Electron',     'slug' => 'electron',                                 'category' => 'framework', 'website_url' => 'https://www.electronjs.org'],
            ['name' => 'Expo',         'slug' => 'expo',                                     'category' => 'framework', 'website_url' => 'https://expo.dev'],
            ['name' => 'Ionic',        'slug' => 'ionic',                                    'category' => 'framework', 'website_url' => 'https://ionicframework.com'],
            ['name' => 'CodeIgniter',  'slug' => 'codeigniter',                              'category' => 'framework', 'website_url' => 'https://www.codeigniter.com'],
            ['name' => 'CakePHP',      'slug' => 'cakephp',                                  'category' => 'framework', 'website_url' => 'https://cakephp.org'],
            ['name' => 'Yii',          'slug' => 'yii',                                      'category' => 'framework', 'website_url' => 'https://www.yiiframework.com'],
            ['name' => 'Phoenix',      'slug' => 'phoenix',                                  'category' => 'framework', 'website_url' => 'https://www.phoenixframework.org'],
            ['name' => 'Fiber',        'slug' => 'fiber',                                    'category' => 'framework', 'website_url' => 'https://gofiber.io'],

            // ═══════════════════════════════════════════════
            // LIBRERÍAS
            // ═══════════════════════════════════════════════
            ['name' => 'React',        'slug' => 'react',         'icon' => 'react.png',      'category' => 'library',   'website_url' => 'https://react.dev'],
            ['name' => 'Solid.js',     'slug' => 'solidjs',                                  'category' => 'library',   'website_url' => 'https://www.solidjs.com'],
            ['name' => 'Qwik',         'slug' => 'qwik',                                     'category' => 'library',   'website_url' => 'https://qwik.dev'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwindcss',   'icon' => 'tailwindcss.png', 'category' => 'library',   'website_url' => 'https://tailwindcss.com'],
            ['name' => 'Bootstrap',    'slug' => 'bootstrap',     'icon' => 'bootstrap.png',  'category' => 'library',   'website_url' => 'https://getbootstrap.com'],
            ['name' => 'TensorFlow',   'slug' => 'tensorflow',    'icon' => 'tensorflow.png', 'category' => 'library',   'website_url' => 'https://www.tensorflow.org'],
            ['name' => 'Livewire',     'slug' => 'livewire',                                 'category' => 'library',   'website_url' => 'https://livewire.laravel.com'],
            ['name' => 'PyTorch',      'slug' => 'pytorch',                                  'category' => 'library',   'website_url' => 'https://pytorch.org'],
            ['name' => 'jQuery',       'slug' => 'jquery',                                   'category' => 'library',   'website_url' => 'https://jquery.com'],
            ['name' => 'Alpine.js',    'slug' => 'alpinejs',                                 'category' => 'library',   'website_url' => 'https://alpinejs.dev'],
            ['name' => 'HTMX',         'slug' => 'htmx',                                     'category' => 'library',   'website_url' => 'https://htmx.org'],
            ['name' => 'RxJS',         'slug' => 'rxjs',                                     'category' => 'library',   'website_url' => 'https://rxjs.dev'],
            ['name' => 'Zustand',      'slug' => 'zustand',                                  'category' => 'library',   'website_url' => 'https://zustand-demo.pmnd.rs'],
            ['name' => 'Redux',        'slug' => 'redux',                                    'category' => 'library',   'website_url' => 'https://redux.js.org'],
            ['name' => 'Three.js',     'slug' => 'threejs',                                  'category' => 'library',   'website_url' => 'https://threejs.org'],
            ['name' => 'D3.js',        'slug' => 'd3js',                                     'category' => 'library',   'website_url' => 'https://d3js.org'],
            ['name' => 'Chart.js',     'slug' => 'chartjs',                                  'category' => 'library',   'website_url' => 'https://www.chartjs.org'],
            ['name' => 'Prisma',       'slug' => 'prisma',                                   'category' => 'library',   'website_url' => 'https://www.prisma.io'],
            ['name' => 'Sequelize',    'slug' => 'sequelize',                                'category' => 'library',   'website_url' => 'https://sequelize.org'],
            ['name' => 'Mongoose',     'slug' => 'mongoose',                                 'category' => 'library',   'website_url' => 'https://mongoosejs.com'],
            ['name' => 'Vitest',       'slug' => 'vitest',                                   'category' => 'library',   'website_url' => 'https://vitest.dev'],
            ['name' => 'Jest',         'slug' => 'jest',                                     'category' => 'library',   'website_url' => 'https://jestjs.io'],
            ['name' => 'Playwright',   'slug' => 'playwright',                               'category' => 'library',   'website_url' => 'https://playwright.dev'],
            ['name' => 'Cypress',      'slug' => 'cypressio',                                'category' => 'library',   'website_url' => 'https://www.cypress.io'],
            ['name' => 'Pandas',       'slug' => 'pandas',                                   'category' => 'library',   'website_url' => 'https://pandas.pydata.org'],
            ['name' => 'NumPy',        'slug' => 'numpy',                                    'category' => 'library',   'website_url' => 'https://numpy.org'],
            ['name' => 'Sass',         'slug' => 'sass',                                     'category' => 'library',   'website_url' => 'https://sass-lang.com'],
            ['name' => 'Less',         'slug' => 'less',                                     'category' => 'library',   'website_url' => 'https://lesscss.org'],

            // ═══════════════════════════════════════════════
            // BASES DE DATOS
            // ═══════════════════════════════════════════════
            ['name' => 'MySQL',        'slug' => 'mysql',         'icon' => 'mysql.png',      'category' => 'database',  'website_url' => 'https://www.mysql.com'],
            ['name' => 'PostgreSQL',   'slug' => 'postgresql',    'icon' => 'postgresql.png', 'category' => 'database',  'website_url' => 'https://www.postgresql.org'],
            ['name' => 'MongoDB',      'slug' => 'mongodb',       'icon' => 'mongodb.png',    'category' => 'database',  'website_url' => 'https://www.mongodb.com'],
            ['name' => 'SQLite',       'slug' => 'sqlite',                                   'category' => 'database',  'website_url' => 'https://www.sqlite.org'],
            ['name' => 'MariaDB',      'slug' => 'mariadb',                                  'category' => 'database',  'website_url' => 'https://mariadb.org'],
            ['name' => 'Redis',        'slug' => 'redis',         'icon' => 'redis.png',      'category' => 'database',  'website_url' => 'https://redis.io'],
            ['name' => 'Cassandra',    'slug' => 'cassandra',                                'category' => 'database',  'website_url' => 'https://cassandra.apache.org'],
            ['name' => 'DynamoDB',     'slug' => 'dynamodb',                                 'category' => 'database',  'website_url' => 'https://aws.amazon.com/dynamodb'],
            ['name' => 'ClickHouse',   'slug' => 'clickhouse',                               'category' => 'database',  'website_url' => 'https://clickhouse.com'],
            ['name' => 'Elasticsearch', 'slug' => 'elasticsearch',                            'category' => 'database',  'website_url' => 'https://www.elastic.co/elasticsearch'],

            // ═══════════════════════════════════════════════
            // HERRAMIENTAS
            // ═══════════════════════════════════════════════
            ['name' => 'Docker',       'slug' => 'docker',        'icon' => 'docker.png',     'category' => 'tool',      'website_url' => 'https://www.docker.com'],
            ['name' => 'Kubernetes',   'slug' => 'kubernetes',    'icon' => 'kubernetes.png', 'category' => 'tool',      'website_url' => 'https://kubernetes.io'],
            ['name' => 'Git',          'slug' => 'git',           'icon' => 'git.png',        'category' => 'tool',      'website_url' => 'https://git-scm.com'],
            ['name' => 'Linux',        'slug' => 'linux',         'icon' => 'linux.png',      'category' => 'tool',      'website_url' => 'https://www.linux.org'],
            ['name' => 'Figma',        'slug' => 'figma',         'icon' => 'figma.png',      'category' => 'tool',      'website_url' => 'https://www.figma.com'],
            ['name' => 'GitHub Actions', 'slug' => 'githubactions',                           'category' => 'tool',      'website_url' => 'https://github.com/features/actions'],
            ['name' => 'Postman',      'slug' => 'postman',                                  'category' => 'tool',      'website_url' => 'https://www.postman.com'],
            ['name' => 'Vite',         'slug' => 'vite',                                     'category' => 'tool',      'website_url' => 'https://vite.dev'],
            ['name' => 'GitHub',       'slug' => 'github',                                   'category' => 'tool',      'website_url' => 'https://github.com'],
            ['name' => 'GitLab',       'slug' => 'gitlab',                                   'category' => 'tool',      'website_url' => 'https://gitlab.com'],
            ['name' => 'Bitbucket',    'slug' => 'bitbucket',                                'category' => 'tool',      'website_url' => 'https://bitbucket.org'],
            ['name' => 'Jira',         'slug' => 'jira',                                     'category' => 'tool',      'website_url' => 'https://www.atlassian.com/software/jira'],
            ['name' => 'Terraform',    'slug' => 'terraform',                                'category' => 'tool',      'website_url' => 'https://www.terraform.io'],
            ['name' => 'Ansible',      'slug' => 'ansible',                                  'category' => 'tool',      'website_url' => 'https://www.ansible.com'],
            ['name' => 'Nginx',        'slug' => 'nginx',                                    'category' => 'tool',      'website_url' => 'https://nginx.org'],
            ['name' => 'Apache',       'slug' => 'apache',                                   'category' => 'tool',      'website_url' => 'https://httpd.apache.org'],
            ['name' => 'Webpack',      'slug' => 'webpack',                                  'category' => 'tool',      'website_url' => 'https://webpack.js.org'],
            ['name' => 'ESLint',       'slug' => 'eslint',                                   'category' => 'tool',      'website_url' => 'https://eslint.org'],
            ['name' => 'Puppeteer',    'slug' => 'puppeteer',                                'category' => 'tool',      'website_url' => 'https://pptr.dev'],
            ['name' => 'Homebrew',     'slug' => 'homebrew',                                 'category' => 'tool',      'website_url' => 'https://brew.sh'],
            ['name' => 'Composer',     'slug' => 'composer',                                 'category' => 'tool',      'website_url' => 'https://getcomposer.org'],
            ['name' => 'Node.js',      'slug' => 'nodejs',        'icon' => 'nodejs.png',     'category' => 'tool',      'website_url' => 'https://nodejs.org'],
            ['name' => 'npm',          'slug' => 'npm',                                      'category' => 'tool',      'website_url' => 'https://www.npmjs.com'],
            ['name' => 'Yarn',         'slug' => 'yarn',                                     'category' => 'tool',      'website_url' => 'https://yarnpkg.com'],
            ['name' => 'pnpm',         'slug' => 'pnpm',                                     'category' => 'tool',      'website_url' => 'https://pnpm.io'],
            ['name' => 'Bun',          'slug' => 'bun',                                      'category' => 'tool',      'website_url' => 'https://bun.sh'],
            ['name' => 'Swagger',      'slug' => 'swagger',                                  'category' => 'tool',      'website_url' => 'https://swagger.io'],
            ['name' => 'Insomnia',     'slug' => 'insomnia',                                 'category' => 'tool',      'website_url' => 'https://insomnia.rest'],
            ['name' => 'Storybook',    'slug' => 'storybook',                                'category' => 'tool',      'website_url' => 'https://storybook.js.org'],
            ['name' => 'Grafana',      'slug' => 'grafana',                                  'category' => 'tool',      'website_url' => 'https://grafana.com'],
            ['name' => 'Prometheus',   'slug' => 'prometheus',                               'category' => 'tool',      'website_url' => 'https://prometheus.io'],
            ['name' => 'Selenium',     'slug' => 'selenium',                                 'category' => 'tool',      'website_url' => 'https://www.selenium.dev'],
            ['name' => 'Jenkins',      'slug' => 'jenkins',                                  'category' => 'tool',      'website_url' => 'https://www.jenkins.io'],
            ['name' => 'Sentry',       'slug' => 'sentry',                                   'category' => 'tool',      'website_url' => 'https://sentry.io'],
            ['name' => 'Datadog',      'slug' => 'datadog',                                  'category' => 'tool',      'website_url' => 'https://www.datadoghq.com'],
            ['name' => 'Rollup',       'slug' => 'rollup',                                   'category' => 'tool',      'website_url' => 'https://rollupjs.org'],

            // ═══════════════════════════════════════════════
            // PLATAFORMAS / CLOUD
            // ═══════════════════════════════════════════════
            ['name' => 'AWS',          'slug' => 'amazonwebservices', 'icon' => 'amazonwebservices.png', 'category' => 'platform', 'website_url' => 'https://aws.amazon.com'],
            ['name' => 'Google Cloud', 'slug' => 'googlecloud',                              'category' => 'platform', 'website_url' => 'https://cloud.google.com'],
            ['name' => 'Azure',        'slug' => 'azure',                                    'category' => 'platform', 'website_url' => 'https://azure.microsoft.com'],
            ['name' => 'Vercel',       'slug' => 'vercel',                                   'category' => 'platform', 'website_url' => 'https://vercel.com'],
            ['name' => 'Netlify',      'slug' => 'netlify',                                  'category' => 'platform', 'website_url' => 'https://www.netlify.com'],
            ['name' => 'Cloudflare',   'slug' => 'cloudflare',                               'category' => 'platform', 'website_url' => 'https://www.cloudflare.com'],
            ['name' => 'Heroku',       'slug' => 'heroku',                                   'category' => 'platform', 'website_url' => 'https://www.heroku.com'],
            ['name' => 'DigitalOcean', 'slug' => 'digitalocean',                             'category' => 'platform', 'website_url' => 'https://www.digitalocean.com'],
            ['name' => 'Firebase',     'slug' => 'firebase',      'icon' => 'firebase.png',   'category' => 'platform', 'website_url' => 'https://firebase.google.com'],
            ['name' => 'Supabase',     'slug' => 'supabase',                                 'category' => 'platform', 'website_url' => 'https://supabase.com'],
            ['name' => 'Algolia',      'slug' => 'algolia',                                  'category' => 'platform', 'website_url' => 'https://www.algolia.com'],
            ['name' => 'Twilio',       'slug' => 'twilio',                                   'category' => 'platform', 'website_url' => 'https://www.twilio.com'],
            ['name' => 'WordPress',    'slug' => 'wordpress',                                'category' => 'platform', 'website_url' => 'https://wordpress.org'],

            // ═══════════════════════════════════════════════
            // PAQUETES
            // ═══════════════════════════════════════════════
            ['name' => 'Laravel Fortify',  'slug' => 'laravel-fortify',   'icon' => null,      'category' => 'package', 'website_url' => 'https://laravel.com/docs/fortify'],
            ['name' => 'Laravel Socialite', 'slug' => 'laravel-socialite', 'icon' => null,     'category' => 'package', 'website_url' => 'https://laravel.com/docs/socialite'],
            ['name' => 'Laravel Reverb',   'slug' => 'laravel-reverb',    'icon' => null,      'category' => 'package', 'website_url' => 'https://reverb.laravel.com'],
            ['name' => 'Laravel Sanctum',  'slug' => 'laravel-sanctum',   'icon' => null,      'category' => 'package', 'website_url' => 'https://laravel.com/docs/sanctum'],
            ['name' => 'Laravel Telescope', 'slug' => 'laravel-telescope', 'icon' => null,     'category' => 'package', 'website_url' => 'https://laravel.com/docs/telescope'],
            ['name' => 'Laravel Sail',     'slug' => 'laravel-sail',      'icon' => null,      'category' => 'package', 'website_url' => 'https://laravel.com/docs/sail'],
            ['name' => 'Laravel Pint',     'slug' => 'laravel-pint',      'icon' => null,      'category' => 'package', 'website_url' => 'https://laravel.com/docs/pint'],
            ['name' => 'Spatie Browsershot', 'slug' => 'spatie-browsershot', 'icon' => null,   'category' => 'package', 'website_url' => 'https://github.com/spatie/browsershot'],
            ['name' => 'Cloudinary',       'slug' => 'cloudinary',        'icon' => 'cloudinary.png', 'category' => 'package', 'website_url' => 'https://cloudinary.com'],
            ['name' => 'Endroid QR Code',  'slug' => 'endroid-qr-code',   'icon' => null,      'category' => 'package', 'website_url' => 'https://github.com/endroid/qr-code'],
            ['name' => 'Pest PHP',         'slug' => 'pest-php',          'icon' => null,      'category' => 'package', 'website_url' => 'https://pestphp.com'],
            ['name' => 'Guzzle',           'slug' => 'guzzle',            'icon' => null,      'category' => 'package', 'website_url' => 'https://docs.guzzlephp.org'],
            ['name' => 'Faker',            'slug' => 'faker-php',         'icon' => null,      'category' => 'package', 'website_url' => 'https://fakerphp.org'],
            ['name' => 'PHPUnit',          'slug' => 'phpunit',           'icon' => null,      'category' => 'package', 'website_url' => 'https://phpunit.de'],
            ['name' => 'Laravel Tinker',   'slug' => 'laravel-tinker',    'icon' => null,      'category' => 'package', 'website_url' => 'https://github.com/laravel/tinker'],
            ['name' => 'Axios',            'slug' => 'axios',             'icon' => 'axios.png',     'category' => 'package', 'website_url' => 'https://axios-http.com'],
            ['name' => 'Laravel Echo',     'slug' => 'laravel-echo',      'icon' => null,      'category' => 'package', 'website_url' => 'https://laravel.com/docs/broadcasting'],
            ['name' => 'Pusher',           'slug' => 'pusher',            'icon' => 'pusher.png',    'category' => 'package', 'website_url' => 'https://pusher.com'],
            ['name' => 'Ziggy',            'slug' => 'ziggy',             'icon' => null,      'category' => 'package', 'website_url' => 'https://github.com/tighten/ziggy'],
            ['name' => 'Lodash',           'slug' => 'lodash',            'icon' => 'lodash.png',    'category' => 'package', 'website_url' => 'https://lodash.com'],
            ['name' => 'SweetAlert2',      'slug' => 'sweetalert2',       'icon' => null,      'category' => 'package', 'website_url' => 'https://sweetalert2.github.io'],
        ];

        foreach ($technologies as $tech) {
            Technology::updateOrCreate(['slug' => $tech['slug']], $tech);
        }
    }
}
