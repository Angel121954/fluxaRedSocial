<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
            'privacy' => fake()->randomElement(['public', 'private']),
            'likes_count' => fake()->numberBetween(0, 100),
            'comments_count' => fake()->numberBetween(0, 50),
            'shares_count' => fake()->numberBetween(0, 20),
        ];
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'privacy' => 'private',
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'privacy' => 'public',
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'likes_count' => fake()->numberBetween(100, 500),
        ]);
    }
}
