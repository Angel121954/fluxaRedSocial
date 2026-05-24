<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('technologies')
            ->where('slug', 'rubyonrails')
            ->update(['slug' => 'rails', 'icon' => 'rails.png']);
    }

    public function down(): void
    {
        DB::table('technologies')
            ->where('slug', 'rails')
            ->update(['slug' => 'rubyonrails', 'icon' => 'rubyonrails.png']);
    }
};
