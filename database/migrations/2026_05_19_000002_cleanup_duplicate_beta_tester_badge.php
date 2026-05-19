<?php

declare(strict_types=1);

use App\Models\Badge;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $duplicate = Badge::where('slug', 'beta_tester')->first();

        if ($duplicate) {
            DB::table('user_badge')->where('badge_id', $duplicate->id)->delete();
            $duplicate->delete();
        }

        Badge::updateOrCreate(
            ['slug' => 'beta-tester'],
            [
                'name' => 'Beta Tester',
                'description' => 'Formaste parte de los primeros usuarios en probar Fluxa en sus inicios',
                'icon' => 'beaker',
                'category' => 'especial',
                'criteria_type' => 'manual',
                'criteria_config' => [],
                'tier' => 3,
                'order' => 99,
            ],
        );
    }

    public function down(): void
    {
        // No re-creamos el duplicado intencionalmente
    }
};
