<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('account_type')
            ->where('role', '!=', 'guest')
            ->update(['account_type' => 'developer']);
    }

    public function down(): void
    {
        // No se puede distinguir entre null original y null rellenado.
    }
};
