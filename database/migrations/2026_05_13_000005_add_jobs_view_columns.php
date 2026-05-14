<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('jobs', 'company_logo')) {
                $table->string('company_logo')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('jobs', 'modality')) {
                $table->string('modality')->nullable()->after('city');
            }
            if (!Schema::hasColumn('jobs', 'modality_label')) {
                $table->string('modality_label')->nullable()->after('modality');
            }
            if (!Schema::hasColumn('jobs', 'salary_currency')) {
                $table->string('salary_currency', 3)->default('USD')->after('salary_max');
            }
            if (!Schema::hasColumn('jobs', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('salary_currency');
            }
            if (!Schema::hasColumn('jobs', 'tags')) {
                $table->json('tags')->nullable()->after('is_featured');
            }
            if (!Schema::hasColumn('jobs', 'location')) {
                $table->string('location')->nullable()->after('city');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $columns = ['company_logo', 'modality', 'modality_label', 'salary_currency', 'is_featured', 'tags', 'location'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('jobs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
