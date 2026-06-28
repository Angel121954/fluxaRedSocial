<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('diaries')) {
            return;
        }

        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->string('emoji', 10)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diaries');
    }
};
