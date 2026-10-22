<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('technology_work_experience')) {
            return;
        }

        // Pivot sin id ni timestamps, igual que project_technology:
        // solo relaciona una experiencia con las tecnologías que usó.
        Schema::create('technology_work_experience', function (Blueprint $table) {
            $table->foreignId('work_experience_id')->constrained('work_experiences')->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained('technologies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technology_work_experience');
    }
};
