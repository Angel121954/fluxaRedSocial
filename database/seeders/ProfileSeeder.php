<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', '!=', 'guest')->get();

        $bios = [
            'Full Stack Developer apasionado por crear soluciones innovadoras. Especializado en Laravel, React y arquitecturas cloud-native.',
            'Backend Developer con 5+ años de experiencia. Amante de las buenas prácticas y el código limpio.',
            'Frontend Developer enfocado en UX/UI. Creo interfaces que los usuarios disfrutan usar.',
            'Mobile Developer | Flutter & React Native | Transformando ideas en apps que la gente ama.',
            'DevOps Engineer | AWS Certified | Automatizando deployments y optimizando infraestructura.',
            'Full Stack Developer | Python & Django | Apasionado por el machine learning y la inteligencia artificial.',
            'Frontend Developer | Vue.js & Nuxt | Especializado en aplicaciones SSR y performance optimizada.',
            'Backend Developer | Node.js & Express | Construyendo APIs escalables y microservicios.',
            'Mobile Developer | Kotlin & Swift | Creando experiencias móviles nativa de alta calidad.',
            'Full Stack Developer | MERN Stack | Interesado en blockchain y Web3.',
        ];

        $locations = [
            'bogotá', 'medellín', 'cali', 'barranquilla', 'cartagena',
            'pereira', 'manizales', 'armenia', 'ibagué', 'villavicencio',
        ];

        foreach ($users as $user) {
            if (! $user->profile) {
                Profile::create([
                    'user_id' => $user->id,
                    'avatar' => 'https://api.dicebear.com/7.x/initials/svg?seed='.$user->username.'&backgroundColor=12b3b6',
                    'bio' => $bios[array_rand($bios)],
                    'location' => $locations[array_rand($locations)],
                    'language' => 'es',
                    'visibility' => 'public',
                ]);
            }
        }

        $this->command->info('Seeded profiles for '.$users->count().' users.');
    }
}
