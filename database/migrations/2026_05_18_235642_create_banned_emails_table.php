<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banned_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->index();
            $table->text('reason')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('banned_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banned_emails');
    }
};
