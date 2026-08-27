<?php

declare(strict_types=1);

use App\Services\LocationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

describe('LocationService', function () {
    it('retorna la lista de países', function () {
        $countries = app(LocationService::class)->getCountries();

        expect($countries)->not->toBeEmpty()
            ->and($countries[0])->toHaveKeys(['name']);
    });

    it('retorna las ciudades de un país existente', function () {
        $service = app(LocationService::class);
        $firstCountry = $service->getCountries()[0]['name'];

        expect($service->getCities($firstCountry))->not->toBeEmpty();
    });

    it('retorna arreglo vacío para un país inexistente', function () {
        expect(app(LocationService::class)->getCities('Narnia'))->toBe([]);
    });

    it('geocodifica una ubicación y devuelve coordenadas', function () {
        Http::fake(['*nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '4.71100', 'lon' => '-74.07209'],
        ])]);

        $result = app(LocationService::class)->geocode('Colombia');

        expect($result)->toBe(['latitude' => 4.711, 'longitude' => -74.07209]);
    });

    it('devuelve null si la API de geocoding falla', function () {
        Http::fake(['*nominatim.openstreetmap.org/*' => Http::response([], 500)]);

        expect(app(LocationService::class)->geocode('Colombia'))->toBeNull();
    });

    it('devuelve null si no hay resultados', function () {
        Http::fake(['*nominatim.openstreetmap.org/*' => Http::response([])]);

        expect(app(LocationService::class)->geocode('Atlántida'))->toBeNull();
    });

    it('reutiliza el resultado en cache', function () {
        Cache::put('geocode_'.md5('Colombia'), ['latitude' => 1.0, 'longitude' => 2.0]);

        expect(app(LocationService::class)->geocode('Colombia'))
            ->toBe(['latitude' => 1.0, 'longitude' => 2.0]);
    });
});
