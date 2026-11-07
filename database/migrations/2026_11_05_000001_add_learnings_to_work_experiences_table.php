<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullable: las experiencias existentes no tienen este dato y no se inventa.
        Schema::table('work_experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('work_experiences', 'learnings')) {
                $table->text('learnings')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_experiences', function (Blueprint $table) {
            if (Schema::hasColumn('work_experiences', 'learnings')) {
                $table->dropColumn('learnings');
            }
        });
    }
};
