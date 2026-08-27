<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'avatar' => 'https://api.dicebear.com/7.x/initials/svg?seed='.fake()->name().'&backgroundColor=12b3b6',
            'cover_image' => null,
            'bio' => fake()->optional()->sentence(10),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'language' => 'es',
            'website_url' => fake()->optional()->url(),
            'github_url' => 'https://github.com/'.fake()->userName(),
            'twitter_url' => 'https://twitter.com/'.fake()->userName(),
            'linkedin_url' => 'https://linkedin.com/in/'.fake()->userName(),
            'visibility' => fake()->randomElement(['public', 'private']),
            'last_seen_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
