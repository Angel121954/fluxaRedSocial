<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();

            $table->string('avatar', 255)->nullable();
            $table->string('cover_image', 255)->nullable();

            $table->text('bio')->nullable();
            $table->string('location', 150)->nullable();
            $table->string('language', 5)->default('es');

            //// Enlaces externos
            $table->string('website_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();

            //// Información adicional opcional
            $table->date('birth_date')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])
                ->nullable();

            //// Control de perfil
            $table->enum('visibility', ['public', 'private'])
                ->default('public');

            $table->timestamp('last_seen_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
