<?php

declare(strict_types=1);

use App\Models\Badge;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Badge::updateOrCreate(
            ['slug' => 'beta_tester'],
            [
                'name' => 'Beta Tester',
                'description' => 'Formaste parte de los primeros usuarios en probar Fluxa en sus inicios',
                'icon' => 'beaker',
                'category' => 'especial',
                'criteria_type' => 'manual',
                'criteria_config' => [],
                'tier' => 1,
                'order' => 99,
            ],
        );
    }

    public function down(): void
    {
        Badge::where('slug', 'beta_tester')->delete();
    }
};
