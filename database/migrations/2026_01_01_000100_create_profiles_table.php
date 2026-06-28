<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('profiles')) {
            return;
        }

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('avatar', 255)->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->text('bio')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 150)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('language', 5)->default('es');
            $table->string('phone_code', 5)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('website_url', 255)->nullable();
            $table->string('github_url', 255)->nullable();
            $table->string('twitter_url', 255)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('visibility')->default('public');
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('accept_messages')->default('1');
            $table->boolean('show_email')->default('0');
            $table->boolean('show_favorites')->default('0');
            $table->string('og_image', 255)->nullable();
            $table->boolean('show_bookmarks')->default('0');
            $table->json('cv_settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
