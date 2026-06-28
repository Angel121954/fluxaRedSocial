<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('badges')) {
            return;
        }

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->string('description', 255);
            $table->string('icon', 50);
            $table->string('category', 50);
            $table->string('criteria_type', 50)->default('count_check');
            $table->json('criteria_config');
            $table->tinyInteger('tier')->unsigned()->default('1');
            $table->smallInteger('order')->unsigned()->default('0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
