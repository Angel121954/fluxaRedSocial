<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jobs')) {
            return;
        }

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name', 150);
            $table->string('company_logo', 255)->nullable();
            $table->string('title', 150);
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->string('location_type');
            $table->string('country', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('modality', 255)->nullable();
            $table->string('modality_label', 255)->nullable();
            $table->string('seniority');
            $table->integer('salary_min')->unsigned()->nullable();
            $table->integer('salary_max')->unsigned()->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->string('whatsapp', 20)->nullable();
            $table->boolean('is_featured')->default('0');
            $table->json('tags')->nullable();
            $table->string('currency', 3)->default('usd');
            $table->string('application_url', 500)->nullable();
            $table->string('application_email', 255)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('published');
            $table->integer('views_count')->unsigned()->default('0');
            $table->integer('applications_count')->unsigned()->default('0');
            $table->tinyInteger('edits_count')->unsigned()->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
