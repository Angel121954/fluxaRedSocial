<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // ── Proyectos ─────────────────────────────────
            [
                'name' => 'Primer proyecto',
                'slug' => 'first-project',
                'description' => 'Publicaste tu primer proyecto en Fluxa',
                'icon' => 'code',
                'category' => 'proyectos',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'projects', 'operator' => '>=', 'value' => 1],
                'tier' => 1,
                'order' => 1,
            ],
            [
                'name' => 'Creador constante',
                'slug' => 'creator-5',
                'description' => 'Publicaste 5 proyectos en Fluxa',
                'icon' => 'archive',
                'category' => 'proyectos',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'projects', 'operator' => '>=', 'value' => 5],
                'tier' => 2,
                'order' => 2,
            ],
            [
                'name' => 'Factoría de código',
                'slug' => 'creator-15',
                'description' => 'Publicaste 15 proyectos en Fluxa',
                'icon' => 'layers',
                'category' => 'proyectos',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'projects', 'operator' => '>=', 'value' => 15],
                'tier' => 3,
                'order' => 3,
            ],

            // ── Comunidad ─────────────────────────────────
            [
                'name' => 'Primer comentario',
                'slug' => 'first-comment',
                'description' => 'Dejaste tu primer comentario en un proyecto',
                'icon' => 'message-square',
                'category' => 'comunidad',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'comments', 'operator' => '>=', 'value' => 1],
                'tier' => 1,
                'order' => 10,
            ],
            [
                'name' => 'Conversador',
                'slug' => 'commenter-25',
                'description' => 'Dejaste 25 comentarios en la plataforma',
                'icon' => 'message-circle',
                'category' => 'comunidad',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'comments', 'operator' => '>=', 'value' => 25],
                'tier' => 2,
                'order' => 11,
            ],

            // ── Social ────────────────────────────────────
            [
                'name' => 'Conectado',
                'slug' => 'followers-10',
                'description' => 'Alcanzaste 10 seguidores en Fluxa',
                'icon' => 'users',
                'category' => 'social',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'followers', 'operator' => '>=', 'value' => 10],
                'tier' => 1,
                'order' => 20,
            ],
            [
                'name' => 'Influencer LATAM',
                'slug' => 'followers-100',
                'description' => 'Alcanzaste 100 seguidores en Fluxa',
                'icon' => 'star',
                'category' => 'social',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'followers', 'operator' => '>=', 'value' => 100],
                'tier' => 2,
                'order' => 21,
            ],
            [
                'name' => 'Referente regional',
                'slug' => 'followers-1000',
                'description' => 'Alcanzaste 1.000 seguidores en Fluxa',
                'icon' => 'award',
                'category' => 'social',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'followers', 'operator' => '>=', 'value' => 1000],
                'tier' => 3,
                'order' => 22,
            ],

            // ── Transparencia salarial ────────────────────
            [
                'name' => 'Donante inicial',
                'slug' => 'salary-first',
                'description' => 'Aportaste tu primer reporte salarial',
                'icon' => 'trending-up',
                'category' => 'transparencia',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'salary_reports', 'operator' => '>=', 'value' => 1],
                'tier' => 1,
                'order' => 30,
            ],
            [
                'name' => 'Donante de datos',
                'slug' => 'salary-5',
                'description' => 'Aportaste 5 reportes salariales',
                'icon' => 'bar-chart',
                'category' => 'transparencia',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'salary_reports', 'operator' => '>=', 'value' => 5],
                'tier' => 2,
                'order' => 31,
            ],
            [
                'name' => 'Transparencia total',
                'slug' => 'salary-15',
                'description' => 'Aportaste 15 reportes salariales',
                'icon' => 'activity',
                'category' => 'transparencia',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'salary_reports', 'operator' => '>=', 'value' => 15],
                'tier' => 3,
                'order' => 32,
            ],

            // ── Perfil ────────────────────────────────────
            [
                'name' => 'Perfil completo',
                'slug' => 'profile-complete',
                'description' => 'Completaste tu perfil al máximo',
                'icon' => 'check-circle',
                'category' => 'perfil',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'profile_completeness', 'operator' => '>=', 'value' => 100],
                'tier' => 2,
                'order' => 40,
            ],

            // ── Especial ──────────────────────────────────
            [
                'name' => 'Founder de Fluxa',
                'slug' => 'founder',
                'description' => 'Creador y fundador de la plataforma Fluxa',
                'icon' => 'flag',
                'category' => 'especial',
                'criteria_type' => 'manual',
                'criteria_config' => [],
                'tier' => 3,
                'order' => 51,
            ],
            [
                'name' => 'Early Adopter',
                'slug' => 'early-adopter',
                'description' => 'Te registraste durante los primeros 30 días de Fluxa',
                'icon' => 'zap',
                'category' => 'especial',
                'criteria_type' => 'manual',
                'criteria_config' => ['model' => 'account_age_days', 'operator' => '<=', 'value' => 30],
                'tier' => 3,
                'order' => 50,
            ],
            [
                'name' => 'Beta Tester',
                'slug' => 'beta-tester',
                'description' => 'Formaste parte de los primeros usuarios en probar Fluxa en sus inicios',
                'icon' => 'shield',
                'category' => 'especial',
                'criteria_type' => 'manual',
                'criteria_config' => [],
                'tier' => 3,
                'order' => 52,
            ],
            [
                'name' => 'Experiencia laboral',
                'slug' => 'first-job',
                'description' => 'Agregaste tu primera experiencia laboral al perfil',
                'icon' => 'briefcase',
                'category' => 'perfil',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'work_experiences', 'operator' => '>=', 'value' => 1],
                'tier' => 1,
                'order' => 41,
            ],
            [
                'name' => 'Stack diverso',
                'slug' => 'stack-5',
                'description' => 'Tienes 5 o más tecnologías en tu stack',
                'icon' => 'cpu',
                'category' => 'perfil',
                'criteria_type' => 'count_check',
                'criteria_config' => ['model' => 'technologies', 'operator' => '>=', 'value' => 5],
                'tier' => 1,
                'order' => 42,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }
}
