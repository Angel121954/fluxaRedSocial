<?php

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $badge = Badge::where('slug', 'founder')->first();
        $user = User::find(1);

        if ($badge && $user) {
            DB::table('user_badge')->updateOrInsert(
                ['user_id' => $user->id, 'badge_id' => $badge->id],
                ['earned_at' => now(), 'notified' => false, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }

    public function down(): void
    {
        $badge = Badge::where('slug', 'founder')->first();
        $user = User::find(1);

        if ($badge && $user) {
            DB::table('user_badge')
                ->where('user_id', $user->id)
                ->where('badge_id', $badge->id)
                ->delete();
        }
    }
};
