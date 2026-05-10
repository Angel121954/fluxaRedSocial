<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('country', 100);
            $table->string('city', 100)->nullable();
            $table->enum('seniority', ['junior', 'mid', 'senior', 'lead']);
            $table->unsignedTinyInteger('experience_years');
            $table->unsignedInteger('salary_usd');
            $table->enum('currency', ['usd'])->default('usd');
            $table->enum('modality', ['remote', 'hybrid', 'onsite']);
            $table->string('company', 150)->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });

        Schema::create('salary_report_technology', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->unique(['salary_report_id', 'technology_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_report_technology');
        Schema::dropIfExists('salary_reports');
    }
};
