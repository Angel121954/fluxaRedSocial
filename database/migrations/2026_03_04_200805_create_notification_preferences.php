<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();

            // Canales
            $table->boolean('email_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);

            // Tipos
            $table->boolean('notify_comments')->default(true);
            $table->boolean('notify_followers')->default(true);
            $table->boolean('notify_mentions')->default(true);
            $table->boolean('weekly_summary')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
