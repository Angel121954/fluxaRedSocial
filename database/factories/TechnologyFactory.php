<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Technology>
 */
class TechnologyFactory extends Factory
{
    protected $model = Technology::class;

    public function definition(): array
    {
        $name = fake()->unique()->programmingLanguage();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/'.Str::slug($name).'/'.Str::slug($name).'-original.svg',
        ];
    }
}
