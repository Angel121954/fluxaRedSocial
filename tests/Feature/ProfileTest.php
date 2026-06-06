<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/configuration')
            ->patch('/configuration', [
                'name' => 'Test User',
                'username' => $user->username,
                'location' => 'Bogotá, Colombia',
                'bio' => 'Desarrollador full stack',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/configuration');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/account', [
                'email' => $user->email,
                'phone_code' => '+57',
                'phone_number' => '3001234567',
                'language' => 'es',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/account');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/account');

        $response->assertRedirect('/login');

        $this->assertGuest();

        $user->refresh();
        $this->assertSame('pending_deletion', $user->status);
    }
}
