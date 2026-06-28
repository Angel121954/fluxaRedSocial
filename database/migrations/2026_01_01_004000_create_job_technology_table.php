<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_technology')) {
            return;
        }

        Schema::create('job_technology', function (Blueprint $table) {
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained('technologies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_technology');
    }
};
