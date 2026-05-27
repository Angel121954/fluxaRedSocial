<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diary_response_id')->constrained('diary_responses')->cascadeOnDelete();
            $table->text('reason');
            $table->timestamps();

            $table->index(['user_id', 'diary_response_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_reports');
    }
};
