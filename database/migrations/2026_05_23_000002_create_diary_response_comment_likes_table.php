<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diary_response_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diary_response_comment_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'diary_response_comment_id'], 'drc_likes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_response_comment_likes');
    }
};
