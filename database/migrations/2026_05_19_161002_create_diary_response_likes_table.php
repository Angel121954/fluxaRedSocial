<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diary_response_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diary_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['diary_response_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_response_likes');
    }
};
