<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notification_preferences')) {
            return;
        }

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('email_enabled')->default('1');
            $table->boolean('push_enabled')->default('1');
            $table->boolean('notify_comments')->default('1');
            $table->boolean('notify_followers')->default('1');
            $table->boolean('notify_mentions')->default('1');
            $table->boolean('weekly_summary')->default('1');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
