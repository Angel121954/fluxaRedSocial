<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('profiles', 'intent')) {
            return;
        }

        Schema::table('profiles', function (Blueprint $table) {
            $table->string('intent', 30)->nullable()->after('specialty');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('profiles', 'intent')) {
            return;
        }

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('intent');
        });
    }
};
