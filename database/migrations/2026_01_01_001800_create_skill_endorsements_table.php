<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('skill_endorsements')) {
            return;
        }

        Schema::create('skill_endorsements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('skill_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_endorsements');
    }
};
