<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('description', 255);
            $table->string('icon', 50);
            $table->string('category', 50);
            $table->string('criteria_type', 50)->default('count_check');
            $table->json('criteria_config');
            $table->unsignedTinyInteger('tier')->default(1);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('user_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at');
            $table->boolean('notified')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badge');
        Schema::dropIfExists('badges');
    }
};
