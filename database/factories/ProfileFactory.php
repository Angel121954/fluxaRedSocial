<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'username' => fake()->unique()->userName(),
            'name' => fake()->name(),
            'avatar' => 'https://api.dicebear.com/7.x/initials/svg?seed='.fake()->name().'&backgroundColor=12b3b6',
            'cover_image' => null,
            'bio' => fake()->optional()->sentence(10),
            'location' => fake()->city(),
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
