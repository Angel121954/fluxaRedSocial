<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GiphyService
{
    private string $apiKey;

    private string $baseUrl = 'https://api.giphy.com/v1/gifs';

    private const int LIMIT = 25;

    public function __construct()
    {
        $this->apiKey = config('services.giphy.api_key', '');
    }

    public function search(string $query, int $offset = 0): array
    {
        if (empty($this->apiKey)) {
            Log::error('GIPHY API key no configurada');
            return [];
        }

        $response = Http::get("{$this->baseUrl}/search", [
            'api_key' => $this->apiKey,
            'q' => $query,
            'limit' => self::LIMIT,
            'offset' => $offset,
            'rating' => 'g',
            'lang' => 'es',
        ]);

        if (! $response->successful()) {
            Log::error('Error al buscar GIFs en GIPHY', [
                'status' => $response->status(),
                'query' => $query,
            ]);
            return [];
        }

        return $this->formatResponse($response->json());
    }

    public function trending(int $offset = 0): array
    {
        if (empty($this->apiKey)) {
            Log::error('GIPHY API key no configurada');
            return [];
        }

        $response = Http::get("{$this->baseUrl}/trending", [
            'api_key' => $this->apiKey,
            'limit' => self::LIMIT,
            'offset' => $offset,
            'rating' => 'g',
        ]);

        if (! $response->successful()) {
            Log::error('Error al obtener trending GIFs de GIPHY', [
                'status' => $response->status(),
            ]);
            return [];
        }

        return $this->formatResponse($response->json());
    }

    private function formatResponse(?array $data): array
    {
        if (empty($data['data'])) {
            return ['gifs' => [], 'total' => 0];
        }

        $gifs = array_map(fn(array $gif): array => [
            'id' => $gif['id'],
            'title' => $gif['title'] ?? '',
            'preview_url' => $gif['images']['fixed_height']['url'] ?? '',
            'original_url' => $gif['images']['original']['url'] ?? '',
            'width' => $gif['images']['fixed_height']['width'] ?? '',
            'height' => $gif['images']['fixed_height']['height'] ?? '',
        ], $data['data']);

        return [
            'gifs' => $gifs,
            'total' => $data['pagination']['total_count'] ?? 0,
        ];
    }
}
