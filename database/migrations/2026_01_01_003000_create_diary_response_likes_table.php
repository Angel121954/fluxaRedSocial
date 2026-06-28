<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('diary_response_likes')) {
            return;
        }

        Schema::create('diary_response_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diary_response_id')->constrained('diary_responses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_response_likes');
    }
};
