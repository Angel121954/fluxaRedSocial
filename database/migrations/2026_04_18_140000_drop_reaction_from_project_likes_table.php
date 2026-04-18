<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_likes', function (Blueprint $table) {
            $table->dropColumn('reaction');
        });
    }

    public function down(): void
    {
        Schema::table('project_likes', function (Blueprint $table) {
            $table->enum('reaction', ['like', 'celebrate', 'support', 'insightful', 'funny'])->default('like')->after('project_id');
        });
    }
};