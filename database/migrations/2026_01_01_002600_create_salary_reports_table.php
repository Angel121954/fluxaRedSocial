<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salary_reports')) {
            return;
        }

        Schema::create('salary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->string('country', 100);
            $table->string('city', 100)->nullable();
            $table->string('seniority');
            $table->tinyInteger('experience_years')->unsigned();
            $table->integer('salary_usd')->unsigned();
            $table->string('currency')->default('usd');
            $table->string('modality');
            $table->string('company', 150)->nullable();
            $table->boolean('verified')->default('0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_reports');
    }
};
