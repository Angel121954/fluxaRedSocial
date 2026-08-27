<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    protected $model = Technology::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'PHP', 'Laravel', 'JavaScript', 'TypeScript', 'Python', 'React', 'Vue',
            'Docker', 'MySQL', 'PostgreSQL', 'Go', 'Rust', 'Ruby', 'Java', 'Node.js',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/'.Str::slug($name).'/'.Str::slug($name).'-original.svg',
        ];
    }
}
