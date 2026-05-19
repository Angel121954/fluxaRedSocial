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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('banned_at')->nullable()->after('status');
            $table->foreignId('banned_by')->nullable()->after('banned_at')
                ->constrained('users')->nullOnDelete();
            $table->string('ban_reason', 255)->nullable()->after('banned_by');
        });

        DB::statement("
            UPDATE users u
            JOIN banned_emails be ON u.email = be.email
            SET
                u.banned_at = be.banned_at,
                u.banned_by = be.banned_by,
                u.ban_reason = be.reason
        ");

        Schema::dropIfExists('banned_emails');
    }

    public function down(): void
    {
        Schema::create('banned_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->index();
            $table->text('reason')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('banned_at')->useCurrent();
            $table->timestamps();
        });

        DB::table('banned_emails')->insertUsing(
            ['email', 'reason', 'banned_by', 'banned_at'],
            DB::table('users')
                ->whereNotNull('banned_at')
                ->select(['email', 'ban_reason', 'banned_by', 'banned_at'])
        );

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['banned_by']);
            $table->dropColumn(['banned_at', 'banned_by', 'ban_reason']);
        });
    }
};
