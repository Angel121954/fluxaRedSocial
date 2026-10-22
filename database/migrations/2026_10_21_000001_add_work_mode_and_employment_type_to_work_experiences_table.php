<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Columnas nuevas nullable: las experiencias ya creadas no tienen este dato
        // y no queremos inventarlo con un default que sería incorrecto.
        Schema::table('work_experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('work_experiences', 'employment_type')) {
                $table->string('employment_type', 20)->nullable()->after('type');
            }

            if (! Schema::hasColumn('work_experiences', 'work_mode')) {
                $table->string('work_mode', 20)->nullable()->after('location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_experiences', function (Blueprint $table) {
            if (Schema::hasColumn('work_experiences', 'employment_type')) {
                $table->dropColumn('employment_type');
            }

            if (Schema::hasColumn('work_experiences', 'work_mode')) {
                $table->dropColumn('work_mode');
            }
        });
    }
};
