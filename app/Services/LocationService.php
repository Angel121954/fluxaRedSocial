<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LocationService
{
    private array $data = [];

    public function __construct()
    {
        $path = database_path('data/countries.json');
        if (file_exists($path)) {
            $this->data = json_decode(file_get_contents($path), true) ?? [];
        }
    }

    public function getCountries(): array
    {
        return Cache::remember('countries', 86400, function () {
            return array_map(fn($item) => [
                'name' => $item['name'],
            ], $this->data);
        });
    }

    public function getCities(string $countryName): array
    {
        $key = 'cities_' . md5($countryName);
        return Cache::remember($key, 86400, function () use ($countryName) {
            foreach ($this->data as $item) {
                if (strtolower($item['name']) === strtolower($countryName)) {
                    return $item['cities'];
                }
            }
            return [];
        });
    }
}
