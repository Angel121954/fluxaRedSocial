<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', '!=', 'guest')->get();
        $technologies = Technology::all();

        if ($users->isEmpty()) {
            $this->command->warn('No se encontraron usuarios para crear proyectos.');

            return;
        }

        $titles = [
            'Sistema de gestión de tareas con notificaciones en tiempo real',
            'API RESTful para aplicación de delivery de comida',
            'Dashboard administrativo con gráficos interactivos',
            'App móvil para meditación y bienestar mental',
            'Plataforma de cursos online con videostreaming',
            'E-commerce con pasarela de pagos integrada',
            'Sistema de reservas para restaurantes',
            'App de seguimiento de hábitos diarios',
            'Portfolio personal con CMS integrado',
            'Chat en tiempo real con websockets',
            'Herramienta de gestión de proyectos ágiles',
            'Red social para desarrolladores',
            'Sistema de facturación electrónica',
            'App de recetas de cocina con IA',
            'Plataforma de crowdfunding para startups',
            'Sistema de control de inventarios',
            'App de traductor instantáneo',
            'Plataforma de networking profesional',
            'Sistema de votaciones electrónicas',
            'App de finanzas personales',
        ];

        $contents = [
            'Desarrollé una aplicación completa utilizando Laravel y Vue.js con arquitectura microservices. Implementé autenticación JWT, manejo de errores centralizado y tests unitarios.',
            'Creé una API robusta con Node.js y Express, documentada con OpenAPI/Swagger. Utilicé MongoDB como base de datos principal y Redis para caché.',
            'Implementé un dashboard interactivo con React y D3.js para visualización de datos. Integré WebSockets para actualizaciones en tiempo real.',
            'Desarrollé una app móvil nativa con Flutter para iOS y Android. Implementé state management con BLoC pattern y notificaciones push.',
            'Construí una plataforma de streaming de video usando AWS MediaConvert y CloudFront. Implementé sistema de suscripciones con Stripe.',
            'Creé un e-commerce completo con carrito de compras, wishlist y checkout optimizado. Integé múltiples pasarelas de pago.',
            'Desarrollé un sistema de reservas con calendario interactivo y confirmaciones automáticas por email. Implementé reporting avanzado.',
            'App de seguimiento de hábitos con gamificación, streaks y recordatorios personalizados. Incluye estadísticas detalladas.',
            'Portfolio profesional con panel CMS para gestión de contenido. Implementé SEO optimizado y sitemap dinámico.',
            'Sistema de chat en tiempo real utilizando Laravel Echo, Pusher y Vue.js. Soporta salas privadas y encriptación.',
        ];

        $privacy = ['public', 'public', 'public', 'private'];

        foreach ($titles as $index => $title) {
            $user = $users->random();

            $project = Project::create([
                'user_id' => $user->id,
                'title' => $title,
                'content' => $contents[array_rand($contents)],
                'privacy' => $privacy[array_rand($privacy)],
                'likes_count' => rand(0, 100),
                'comments_count' => rand(0, 30),
                'shares_count' => rand(0, 15),
            ]);

            if ($technologies->isNotEmpty()) {
                $projectTechnologies = $technologies->random(rand(2, 5));
                $project->technologies()->attach($projectTechnologies->pluck('id'));
            }

            $numImages = rand(1, 4);
            for ($i = 0; $i < $numImages; $i++) {
                ProjectMedia::create([
                    'project_id' => $project->id,
                    'media_url' => 'https://picsum.photos/800/600?random='.rand(1, 1000),
                    'type' => 'image',
                    'position' => $i,
                ]);
            }
        }

        $this->command->info('Seeded '.count($titles).' projects con imágenes.');
    }
}
