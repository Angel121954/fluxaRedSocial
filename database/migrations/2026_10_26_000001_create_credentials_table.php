<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('credentials')) {
            return;
        }

        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('issuer', 150);
            $table->string('type', 20)->default('certification');
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->string('credential_id', 100)->nullable();
            $table->string('credential_url', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('privacy', 20)->default('public');
            $table->timestamps();

            $table->index(['user_id', 'privacy']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
