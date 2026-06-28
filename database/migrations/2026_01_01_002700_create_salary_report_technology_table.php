<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salary_report_technology')) {
            return;
        }

        Schema::create('salary_report_technology', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_report_id')->constrained('salary_reports')->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained('technologies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_report_technology');
    }
};
