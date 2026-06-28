<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conversations')) {
            return;
        }

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_a_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_b_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_pinned')->default('0');
            $table->timestamp('pinned_at')->nullable();
            $table->timestamp('deleted_by_a_at')->nullable();
            $table->timestamp('deleted_by_b_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
