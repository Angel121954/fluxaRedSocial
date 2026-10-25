<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $duplicates = DB::table('users')
            ->select('provider', 'provider_id', DB::raw('MIN(id) as keep_id'))
            ->whereNotNull('provider')
            ->whereNotNull('provider_id')
            ->groupBy('provider', 'provider_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('users')
                ->where('provider', $duplicate->provider)
                ->where('provider_id', $duplicate->provider_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->update([
                    'provider' => null,
                    'provider_id' => null,
                ]);
        }

        if (! Schema::hasIndex('users', ['provider', 'provider_id'], 'unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique(['provider', 'provider_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasIndex('users', ['provider', 'provider_id'], 'unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['provider', 'provider_id']);
            });
        }
    }
};
