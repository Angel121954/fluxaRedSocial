<?php

namespace Database\Factories;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Suggestion>
 */
class SuggestionFactory extends Factory
{
    protected $model = Suggestion::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->paragraph(3),
            'image_path' => null,
            'status' => fake()->randomElement(['pending', 'approved', 'reviewing', 'rejected']),
        ];
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => 'https://picsum.photos/800/600?random='.fake()->unique()->numberBetween(1, 10000),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }
}
