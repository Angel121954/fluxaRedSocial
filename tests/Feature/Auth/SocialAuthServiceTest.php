<?php

declare(strict_types=1);

use App\Exceptions\SocialAuthException;
use App\Models\User;
use App\Notifications\VerifyEmail;
use App\Services\SocialAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Contracts\User as SocialiteUser;

uses(RefreshDatabase::class);

class FakeSocialiteUser implements SocialiteUser
{
    public ?string $token = null;

    public ?string $refreshToken = null;

    public ?int $expiresIn = null;

    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private readonly bool $emailVerified = true,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getNickname(): string
    {
        return 'socialuser';
    }

    public function getName(): string
    {
        return 'Social User';
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAvatar(): ?string
    {
        return null;
    }

    public function getRaw(): array
    {
        return ['email_verified' => $this->emailVerified];
    }
}

function socialAuthService(): SocialAuthService
{
    return app(SocialAuthService::class);
}

describe('Vinculación de cuentas sociales', function () {
    it('vincula una cuenta local verificada cuando el proveedor confirma el correo', function () {
        $user = User::factory()->create([
            'email' => 'social@example.com',
            'provider' => null,
            'provider_id' => null,
        ]);

        $result = socialAuthService()->findOrCreateUser(
            'google',
            new FakeSocialiteUser('google-1', 'social@example.com'),
        );

        expect($result->id)->toBe($user->id)
            ->and($result->fresh()->provider)->toBe('google')
            ->and($result->fresh()->provider_id)->toBe('google-1');
    });

    it('rechaza vincular una cuenta local sin verificar', function () {
        $user = User::factory()->unverified()->create([
            'email' => 'social@example.com',
            'provider' => null,
            'provider_id' => null,
        ]);

        expect(fn () => socialAuthService()->findOrCreateUser(
            'google',
            new FakeSocialiteUser('google-1', 'social@example.com'),
        ))->toThrow(SocialAuthException::class);

        expect($user->fresh()->provider)->toBeNull();
    });

    it('rechaza vincular cuando el proveedor no confirma el correo', function () {
        $user = User::factory()->create([
            'email' => 'social@example.com',
            'provider' => null,
            'provider_id' => null,
        ]);

        expect(fn () => socialAuthService()->findOrCreateUser(
            'google',
            new FakeSocialiteUser('google-1', 'social@example.com', false),
        ))->toThrow(SocialAuthException::class);

        expect($user->fresh()->provider)->toBeNull();
    });

    it('rechaza vincular una cuenta con otro proveedor', function () {
        User::factory()->create([
            'email' => 'social@example.com',
            'provider' => 'github',
            'provider_id' => 'github-1',
        ]);

        expect(fn () => socialAuthService()->findOrCreateUser(
            'google',
            new FakeSocialiteUser('google-1', 'social@example.com'),
        ))->toThrow(SocialAuthException::class);
    });

    it('crea el usuario como verificado cuando el proveedor confirma el correo', function () {
        $user = socialAuthService()->findOrCreateUser(
            'google',
            new FakeSocialiteUser('google-2', 'nuevo@example.com'),
        );

        expect($user->email_verified_at)->not->toBeNull()
            ->and($user->provider)->toBe('google')
            ->and($user->provider_id)->toBe('google-2');
    });

    it('verifica el correo de GitHub contra el endpoint de emails', function () {
        Http::fake([
            'api.github.com/user/emails' => Http::response([
                ['email' => 'github@example.com', 'primary' => true, 'verified' => true],
            ]),
            'api.github.com/user' => Http::response([
                'login' => 'socialuser',
                'public_repos' => 3,
            ]),
        ]);

        $socialUser = new FakeSocialiteUser('github-1', 'github@example.com');
        $socialUser->token = 'github-token';

        $user = socialAuthService()->findOrCreateUser('github', $socialUser);

        expect($user->email_verified_at)->not->toBeNull()
            ->and($user->provider)->toBe('github')
            ->and($user->provider_id)->toBe('github-1');
    });

    it('crea el usuario sin verificar y envía la verificación cuando el proveedor no confirma el correo', function () {
        Notification::fake();

        $user = socialAuthService()->findOrCreateUser(
            'google',
            new FakeSocialiteUser('google-3', 'nuevo@example.com', false),
        );

        expect($user->email_verified_at)->toBeNull();

        Notification::assertSentTo($user, VerifyEmail::class);
    });
});
