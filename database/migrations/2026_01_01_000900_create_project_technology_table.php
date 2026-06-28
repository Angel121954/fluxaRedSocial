<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_technology')) {
            return;
        }

        Schema::create('project_technology', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained('technologies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_technology');
    }
};
