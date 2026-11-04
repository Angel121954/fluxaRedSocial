<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        $usedSlugs = [];

        DB::table('projects')
            ->select('id', 'title')
            ->chunkById(200, function ($projects) use (&$usedSlugs): void {
                foreach ($projects as $project) {
                    $base = Str::slug($project->title) ?: 'proyecto';
                    $slug = $base;
                    $suffix = 2;

                    while (in_array($slug, $usedSlugs, true)) {
                        $slug = $base.'-'.$suffix++;
                    }

                    $usedSlugs[] = $slug;

                    DB::table('projects')
                        ->where('id', $project->id)
                        ->update(['slug' => $slug]);
                }
            });

        Schema::table('projects', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
