<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('status', 'pending_deletion')
            ->whereNull('delete_at')
            ->orderBy('id')
            ->chunkById(500, function ($users): void {
                foreach ($users as $user) {
                    $deleteAt = $user->deleted_at
                        ? Carbon::parse($user->deleted_at)
                        : Carbon::parse($user->updated_at ?? now())->addDays(30);

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['delete_at' => $deleteAt]);
                }
            });
    }

    public function down(): void {}
};
