<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('analytics_events')) {
            return;
        }

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 80);
            $table->json('properties')->nullable();
            $table->string('source', 80)->nullable();
            $table->string('medium', 80)->nullable();
            $table->string('campaign', 120)->nullable();
            $table->string('referrer', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['name', 'created_at']);
            $table->index(['user_id', 'name']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
