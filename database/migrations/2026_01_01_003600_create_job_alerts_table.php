<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_alerts')) {
            return;
        }

        Schema::create('job_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('keyword', 255)->nullable();
            $table->string('modality', 255)->nullable();
            $table->string('frequency', 255)->default('instant');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_alerts');
    }
};
