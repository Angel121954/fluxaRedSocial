<?php

namespace Database\Seeders;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuggestionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', '!=', 'guest')->get();

        if ($users->isEmpty()) {
            $this->command->warn('No se encontraron usuarios para crear sugerencias.');

            return;
        }

        $descriptions = [
            'Sería genial poder agregar proyectos colaborativos donde varios desarrolladores puedan trabajar juntos en tiempo real.',
            'Necesito una forma de exportar mi portfolio en PDF con un diseño profesional.',
            'Me encantaría ver notificaciones push cuando alguien le dé like a mis proyectos.',
            'Sería útil tener un chat integrado para comunicarme con otros desarrolladores.',
            'La función de buscar por tecnologías es excelente, pero podría mejorar con filtros avanzados.',
            'Quisiera poder conectar mi cuenta de GitHub automáticamente para importar mis repositorios.',
            'Un sistema de recomendaciones basado en mis tecnologías favoritas sería muy útil.',
            'Me gustaría seguir categorías o temas específicos y recibir actualizaciones.',
            'Una integración con LinkedIn para importar mi CV automáticamente sería genial.',
            'El onboarding fue muy intuitivo, pero echó de menos tutoriales en video.',
            'Sería genial poder marcar proyectos como destacados y que aparezcan en el perfil.',
            'Un modo oscuro para la interfaz mejoraría mucho la experiencia nocturna.',
            'Me gustaría recibir un resumen semanal de actividad en mi correo electrónico.',
            'La opción de compartir proyectos directamente en redes sociales sería útil.',
            'Un sistema de logros o badges motivaría a los usuarios a completar sus perfiles.',
            'Sería genial tener acceso a una API pública para que otros puedan integrar Fluxa.',
            'La velocidad de carga de las imágenes podría mejorarse con lazy loading más agresivo.',
            'Quisiera poder ver estadísticas detalladas de visitas a mi perfil.',
            'Una función de comparar tu perfil con otros devs inspiraría a mejorar.',
            'Un chat en vivo con soporte técnico sería muy útil para resolver dudas rápidamente.',
        ];

        $statuses = ['pending', 'approved', 'reviewing', 'rejected'];

        foreach ($descriptions as $description) {
            Suggestion::create([
                'user_id' => $users->random()->id,
                'description' => $description,
                'image_path' => null,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        $this->command->info('Seeded '.count($descriptions).' suggestions.');
    }
}
