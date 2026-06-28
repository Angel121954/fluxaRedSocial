<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('username', 255);
            $table->string('role', 20);
            $table->string('account_type', 255)->nullable();
            $table->string('email', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('onboarding_completed', 20)->nullable();
            $table->string('provider', 255)->nullable();
            $table->string('provider_id', 255)->nullable();
            $table->text('github_token')->nullable();
            $table->text('github_refresh_token')->nullable();
            $table->timestamp('github_token_expires_at')->nullable();
            $table->string('github_username', 255)->nullable();
            $table->integer('github_public_repos')->nullable()->default('0');
            $table->timestamp('github_synced_at')->nullable();
            $table->string('status', 50)->default('activo');
            $table->timestamp('banned_at')->nullable();
            $table->foreignId('banned_by')->constrained('users')->nullOnDelete();
            $table->string('ban_reason', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
