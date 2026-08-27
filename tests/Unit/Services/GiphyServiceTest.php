<?php

declare(strict_types=1);

use App\Services\GiphyService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Config::set('services.giphy.api_key', 'test-key');
});

function giphyJson(): array
{
    return [
        'data' => [[
            'id' => 'gif1',
            'title' => 'Awesome GIF',
            'images' => [
                'fixed_height' => ['url' => 'https://cdn.example/fh.gif', 'width' => '200', 'height' => '150'],
                'original' => ['url' => 'https://cdn.example/orig.gif'],
            ],
        ]],
        'pagination' => ['total_count' => 42],
    ];
}

describe('GiphyService', function () {
    it('retorna arreglo vacío si no hay API key configurada', function () {
        Config::set('services.giphy.api_key', '');

        expect(app(GiphyService::class)->search('laravel'))->toBe([]);
    });

    it('busca GIFs y formatea la respuesta', function () {
        Http::fake(['api.giphy.com/*' => Http::response(giphyJson())]);

        $result = app(GiphyService::class)->search('php', 10);

        expect($result['total'])->toBe(42)
            ->and($result['gifs'])->toHaveCount(1)
            ->and($result['gifs'][0])->toMatchArray([
                'id' => 'gif1',
                'title' => 'Awesome GIF',
                'preview_url' => 'https://cdn.example/fh.gif',
                'original_url' => 'https://cdn.example/orig.gif',
                'width' => '200',
                'height' => '150',
            ]);
    });

    it('retorna arreglo vacío si la API responde con error', function () {
        Http::fake(['api.giphy.com/*' => Http::response([], 500)]);

        expect(app(GiphyService::class)->search('php'))->toBe([]);
    });

    it('retorna GIFs trending formateados', function () {
        Http::fake(['api.giphy.com/*' => Http::response(giphyJson())]);

        $result = app(GiphyService::class)->trending();

        expect($result['gifs'])->toHaveCount(1)
            ->and($result['gifs'][0]['id'])->toBe('gif1');
    });

    it('retorna conteo cero cuando no hay data', function () {
        Http::fake(['api.giphy.com/*' => Http::response(['data' => []])]);

        expect(app(GiphyService::class)->trending())->toBe(['gifs' => [], 'total' => 0]);
    });
});
