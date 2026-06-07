<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('media_type')->nullable()->after('body');
            $table->text('media_url')->nullable()->after('media_type');
            $table->string('media_name')->nullable()->after('media_url');
            $table->unsignedInteger('media_size')->nullable()->after('media_name');
            $table->string('public_id')->nullable()->after('media_size');
            $table->text('body')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_url', 'media_name', 'media_size', 'public_id']);
            $table->text('body')->nullable(false)->change();
        });
    }
};
