<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('messages')) {
            return;
        }

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->string('media_type', 255)->nullable();
            $table->text('media_url')->nullable();
            $table->string('media_name', 255)->nullable();
            $table->integer('media_size')->unsigned()->nullable();
            $table->string('public_id', 255)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->foreignId('parent_id')->constrained('messages')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
