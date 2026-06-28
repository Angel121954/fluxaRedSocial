<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('soft_skills')) {
            return;
        }

        Schema::create('soft_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('skill_templates')->nullOnDelete();
            $table->string('name', 100);
            $table->string('category')->default('other');
            $table->tinyInteger('level')->default('3');
            $table->integer('order')->default('0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soft_skills');
    }
};
