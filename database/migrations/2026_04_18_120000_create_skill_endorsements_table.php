<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_endorsements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('skill_type', [
                'technical_communication',
                'logical_thinking',
                'collaboration',
                'architecture',
                'leadership'
            ]);
            $table->timestamps();
            $table->unique(['user_id', 'project_id', 'skill_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_endorsements');
    }
};