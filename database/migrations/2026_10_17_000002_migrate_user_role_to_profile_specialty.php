<?php

declare(strict_types=1);

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const ACCESS_ROLES = ['guest', 'user', 'admin'];

    public function up(): void
    {
        User::query()
            ->whereNotIn('role', self::ACCESS_ROLES)
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    $user->profile?->update(['specialty' => $user->role]);
                    $user->update(['role' => 'user']);
                }
            });
    }

    public function down(): void
    {
        Profile::query()
            ->whereNotNull('specialty')
            ->chunkById(200, function ($profiles) {
                foreach ($profiles as $profile) {
                    $profile->user?->update(['role' => $profile->specialty]);
                    $profile->update(['specialty' => null]);
                }
            });
    }
};
