<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateUser5DataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 5;

        DB::table('work_experiences')->insert([
            [
                'user_id' => $userId,
                'company' => 'TechCorp Solutions',
                'position' => 'Senior Full Stack Developer',
                'location' => 'Madrid, España',
                'started_at' => '2022-01-15',
                'ended_at' => null,
                'current' => true,
                'description' => 'Desarrollo de aplicaciones web escalables usando Laravel y Vue.js. Liderazgo técnico de equipo de 5 desarrolladores.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'company' => 'Digital Agency S.L.',
                'position' => 'Backend Developer',
                'location' => 'Barcelona, España',
                'started_at' => '2019-06-01',
                'ended_at' => '2021-12-31',
                'current' => false,
                'description' => 'Desarrollo de APIs RESTful y servicios backend con PHP y Node.js. Implementación de arquitecturas microservices.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'company' => 'StartUp Innova',
                'position' => 'Junior Developer',
                'location' => 'Valencia, España',
                'started_at' => '2017-09-01',
                'ended_at' => '2019-05-30',
                'current' => false,
                'description' => 'Desarrollo frontend con React y mantenimiento de bases de datos MySQL.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('educations')->insert([
            [
                'user_id' => $userId,
                'institution' => 'Universitat Politècnica de València',
                'degree' => 'Grado en Ingeniería Informática',
                'field' => 'Desarrollo de Software',
                'graduated_year' => 2017,
                'current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'institution' => 'Coursera & Udacity',
                'degree' => 'Various Certifications',
                'field' => 'Cloud Computing & DevOps',
                'graduated_year' => null,
                'current' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $projectId = DB::table('projects')->insertGetId([
            'user_id' => $userId,
            'title' => 'Sistema de Gestión de Proyectos',
            'content' => 'Plataforma integral para gestión de proyectos con características de Kanban, tiempos y reportes. Incluye autenticación, roles, panel de administración y API REST.',
            'privacy' => 'public',
            'likes_count' => 0,
            'comments_count' => 0,
            'shares_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('project_technology')->insert([
            ['project_id' => $projectId, 'technology_id' => 1],
            ['project_id' => $projectId, 'technology_id' => 2],
            ['project_id' => $projectId, 'technology_id' => 3],
        ]);

        $projectId2 = DB::table('projects')->insertGetId([
            'user_id' => $userId,
            'title' => 'E-commerce con Carrito de Compras',
            'content' => 'Tienda online completa con pasarela de pagos, inventario, gestión de pedidos y panel de administración. Diseño responsive y optimización SEO.',
            'privacy' => 'public',
            'likes_count' => 0,
            'comments_count' => 0,
            'shares_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('project_technology')->insert([
            ['project_id' => $projectId2, 'technology_id' => 1],
            ['project_id' => $projectId2, 'technology_id' => 4],
            ['project_id' => $projectId2, 'technology_id' => 5],
        ]);

        $projectId3 = DB::table('projects')->insertGetId([
            'user_id' => $userId,
            'title' => 'API REST para App Móvil',
            'content' => 'Backend completo para aplicación móvil de recetas de cocina. Implementación de autenticación JWT, base de datos PostgreSQL, cache con Redis y documentación con Swagger.',
            'privacy' => 'public',
            'likes_count' => 0,
            'comments_count' => 0,
            'shares_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('project_technology')->insert([
            ['project_id' => $projectId3, 'technology_id' => 2],
            ['project_id' => $projectId3, 'technology_id' => 6],
            ['project_id' => $projectId3, 'technology_id' => 7],
        ]);

        $profile = DB::table('profiles')->where('user_id', $userId)->first();
        if ($profile) {
            DB::table('profiles')->where('user_id', $userId)->update([
                'bio' => 'Desarrollador Full Stack con más de 6 años de experiencia en el sector tecnológico. Apasionado por las nuevas tecnologías, el código limpio y la resolución de problemas complejos. Siempre en búsqueda de nuevos desafíos que me permitan crecer profesionalmente.',
                'location' => 'Madrid, España',
                'website_url' => 'https://midominio.dev',
                'linkedin_url' => 'https://linkedin.com/in/username',
                'twitter_url' => 'https://twitter.com/username',
                'github_url' => 'https://github.com/username',
                'updated_at' => now(),
            ]);
        }

        echo "Datos creados para user_id=$userId:\n";
        echo "- 3 experiencias laborales\n";
        echo "- 2 educaciones\n";
        echo "- 3 proyectos con tecnologías\n";
        echo "- Datos de perfil actualizados\n";
    }
}
