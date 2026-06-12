<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function geocode(string $country, ?string $city = null): ?array
    {
        $query = $city ? "{$city}, {$country}" : $country;
        $cacheKey = 'geocode_' . md5($query);

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Fluxa/1.0 (social network for devs)',
                'Accept-Language' => 'es',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 0,
            ]);

            if ($response->failed() || empty($response->json())) {
                return null;
            }

            $data = $response->json()[0];

            $result = [
                'latitude' => (float) $data['lat'],
                'longitude' => (float) $data['lon'],
            ];

            Cache::put($cacheKey, $result, 86400 * 30);

            return $result;
        } catch (\Throwable $e) {
            Log::warning('Error en geocoding', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
