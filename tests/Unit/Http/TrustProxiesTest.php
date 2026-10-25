<?php

declare(strict_types=1);

use App\Http\Middleware\TrustProxies;
use Tests\TestCase;

uses(TestCase::class);

function trustedProxiesFrom(?string $value): mixed
{
    config(['app.trusted_proxies' => $value]);

    $middleware = new TrustProxies;

    return (new ReflectionProperty(TrustProxies::class, 'proxies'))->getValue($middleware);
}

describe('TrustProxies', function () {
    it('no confía en proxies por defecto', function () {
        expect(trustedProxiesFrom(null))->toBeNull()
            ->and(trustedProxiesFrom(''))->toBeNull();
    });

    it('parsea una lista de proxies separada por comas', function () {
        expect(trustedProxiesFrom('10.0.0.1, 10.0.0.2'))->toBe(['10.0.0.1', '10.0.0.2']);
    });

    it('conserva el comodín solo si se configura explícitamente', function () {
        expect(trustedProxiesFrom('*'))->toBe('*');
    });
});
