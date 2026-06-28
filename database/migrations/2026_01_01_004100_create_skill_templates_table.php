<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('skill_templates')) {
            return;
        }

        Schema::create('skill_templates', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name', 100);
            $table->boolean('is_active')->default('1');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_templates');
    }
};
