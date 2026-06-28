<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('diary_responses')) {
            return;
        }

        Schema::create('diary_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diary_id')->constrained('diaries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->integer('likes_count')->unsigned()->default('0');
            $table->integer('comments_count')->unsigned()->default('0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_responses');
    }
};
