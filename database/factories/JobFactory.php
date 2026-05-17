<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $seniority = $this->faker->randomElement(['junior', 'mid', 'senior', 'lead']);
        $modality = $this->faker->randomElement(['remoto', 'hibrido', 'presencial']);

        $modalityLabels = [
            'remoto' => ['label' => 'Remoto', 'location_type' => 'remote'],
            'hibrido' => ['label' => 'Híbrido', 'location_type' => 'hybrid'],
            'presencial' => ['label' => 'Presencial', 'location_type' => 'onsite'],
        ];

        $countries = ['Colombia', 'México', 'Argentina', 'Chile', 'Perú', 'Ecuador', 'Uruguay', 'Costa Rica'];
        $cities = [
            'Colombia' => ['Bogotá', 'Medellín', 'Cali', 'Barranquilla'],
            'México' => ['Ciudad de México', 'Guadalajara', 'Monterrey'],
            'Argentina' => ['Buenos Aires', 'Córdoba', 'Rosario'],
            'Chile' => ['Santiago', 'Valparaíso', 'Concepción'],
            'Perú' => ['Lima', 'Arequipa', 'Cusco'],
            'Ecuador' => ['Quito', 'Guayaquil'],
            'Uruguay' => ['Montevideo'],
            'Costa Rica' => ['San José'],
        ];

        $country = $this->faker->randomElement($countries);
        $city = $this->faker->randomElement($cities[$country]);

        $salaryRanges = [
            'junior' => [800, 2000],
            'mid' => [2000, 4500],
            'senior' => [4000, 8000],
            'lead' => [6000, 12000],
        ];

        $min = $this->faker->numberBetween($salaryRanges[$seniority][0], $salaryRanges[$seniority][1] - 500);
        $max = $min + $this->faker->numberBetween(500, 3000);

        $locations = [
            'remoto' => $country === 'México' || $country === 'Colombia' ? "Latinoamérica" : "Remoto desde {$country}",
            'hibrido' => "{$city}, {$country}",
            'presencial' => "{$city}, {$country}",
        ];

        return [
            'user_id' => \App\Models\User::factory(),
            'company_name' => $this->faker->company(),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => "• " . implode("\n• ", $this->faker->sentences(4)),
            'benefits' => "• " . implode("\n• ", $this->faker->sentences(3)),
            'application_url' => $this->faker->boolean(40) ? $this->faker->url() : null,
            'location_type' => $modalityLabels[$modality]['location_type'],
            'modality' => $modality,
            'modality_label' => $modalityLabels[$modality]['label'],
            'country' => $country,
            'city' => $modality === 'remoto' ? 'Remoto' : $city,
            'location' => $locations[$modality],
            'seniority' => $seniority,
            'salary_min' => $min,
            'salary_max' => $max,
            'currency' => 'usd',
            'salary_currency' => 'USD',
            'whatsapp' => $this->faker->boolean(30) ? '57' . $this->faker->numerify('3########') : null,
            'is_featured' => $this->faker->boolean(15),
            'status' => $this->faker->randomElement(['published', 'published', 'published', 'draft']),
            'expires_at' => $this->faker->dateTimeBetween('+15 days', '+60 days'),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn() => ['is_featured' => true]);
    }

    public function remote(): static
    {
        return $this->state(fn() => [
            'modality' => 'remoto',
            'modality_label' => 'Remoto',
            'location_type' => 'remote',
            'city' => 'Remoto',
            'location' => 'Latinoamérica',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn() => ['status' => 'published']);
    }
}
