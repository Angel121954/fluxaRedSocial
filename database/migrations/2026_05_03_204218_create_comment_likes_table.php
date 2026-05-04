<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('comment_likes')) {
            \Illuminate\Support\Facades\DB::statement("
                CREATE TABLE comment_likes (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT UNSIGNED NOT NULL,
                    comment_id BIGINT UNSIGNED NOT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    UNIQUE KEY unique_user_comment (user_id, comment_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE
                )
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_likes');
    }
};
