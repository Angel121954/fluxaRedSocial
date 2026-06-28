<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            return;
        }

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 255);
            $table->string('title', 255);
            $table->text('body');
            $table->string('link', 255)->nullable();
            $table->foreignId('from_user_id')->constrained('users')->nullOnDelete();
            $table->bigInteger('reference_id')->unsigned()->nullable();
            $table->string('reference_type', 255)->nullable();
            $table->boolean('is_read')->default('0');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
