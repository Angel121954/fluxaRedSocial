<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            return;
        }

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 100);
            $table->text('content')->nullable();
            $table->string('privacy')->default('public');
            $table->integer('likes_count')->unsigned()->default('0');
            $table->integer('comments_count')->unsigned()->default('0');
            $table->integer('shares_count')->unsigned()->default('0');
            $table->foreignId('parent_id')->constrained('projects');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
