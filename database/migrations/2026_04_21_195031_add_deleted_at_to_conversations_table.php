<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->timestamp('deleted_by_a_at')->nullable()->after('updated_at');
            $table->timestamp('deleted_by_b_at')->nullable()->after('deleted_by_a_at');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['deleted_by_a_at', 'deleted_by_b_at']);
        });
    }
};
