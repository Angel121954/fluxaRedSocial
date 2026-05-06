<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function hasIndex(string $table, string $indexName): bool
    {
        $result = DB::select(
            "SELECT COUNT(*) as cnt FROM information_schema.statistics 
             WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
            [$table, $indexName]
        );
        
        return $result[0]->cnt > 0;
    }

    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! $this->hasIndex('projects', 'projects_user_created_index')) {
                $table->index(['user_id', 'created_at'], 'projects_user_created_index');
            }
            if (! $this->hasIndex('projects', 'projects_privacy_index')) {
                $table->index('privacy', 'projects_privacy_index');
            }
        });

        Schema::table('follows', function (Blueprint $table) {
            if (! $this->hasIndex('follows', 'follows_follower_index')) {
                $table->index('follower_id', 'follows_follower_index');
            }
            if (! $this->hasIndex('follows', 'follows_followed_index')) {
                $table->index('followed_id', 'follows_followed_index');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (! $this->hasIndex('messages', 'messages_conv_created_index')) {
                $table->index(['conversation_id', 'created_at'], 'messages_conv_created_index');
            }
            if (! $this->hasIndex('messages', 'messages_sender_read_index')) {
                $table->index(['sender_id', 'read_at'], 'messages_sender_read_index');
            }
        });

        Schema::table('project_likes', function (Blueprint $table) {
            if (! $this->hasIndex('project_likes', 'project_likes_project_index')) {
                $table->index('project_id', 'project_likes_project_index');
            }
        });

        Schema::table('project_bookmarks', function (Blueprint $table) {
            if (! $this->hasIndex('project_bookmarks', 'project_bookmarks_project_index')) {
                $table->index('project_id', 'project_bookmarks_project_index');
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            if (! $this->hasIndex('comments', 'comments_project_created_index')) {
                $table->index(['project_id', 'created_at'], 'comments_project_created_index');
            }
            if (! $this->hasIndex('comments', 'comments_user_index')) {
                $table->index('user_id', 'comments_user_index');
            }
        });

        Schema::table('skill_endorsements', function (Blueprint $table) {
            if (! $this->hasIndex('skill_endorsements', 'endorsements_project_skill_index')) {
                $table->index(['project_id', 'skill_type'], 'endorsements_project_skill_index');
            }
            if (! $this->hasIndex('skill_endorsements', 'endorsements_user_index')) {
                $table->index('user_id', 'endorsements_user_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if ($this->hasIndex('projects', 'projects_user_created_index')) {
                $table->dropIndex('projects_user_created_index');
            }
            if ($this->hasIndex('projects', 'projects_privacy_index')) {
                $table->dropIndex('projects_privacy_index');
            }
        });

        Schema::table('follows', function (Blueprint $table) {
            if ($this->hasIndex('follows', 'follows_follower_index')) {
                $table->dropIndex('follows_follower_index');
            }
            if ($this->hasIndex('follows', 'follows_followed_index')) {
                $table->dropIndex('follows_followed_index');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if ($this->hasIndex('messages', 'messages_conv_created_index')) {
                $table->dropIndex('messages_conv_created_index');
            }
            if ($this->hasIndex('messages', 'messages_sender_read_index')) {
                $table->dropIndex('messages_sender_read_index');
            }
        });

        Schema::table('project_likes', function (Blueprint $table) {
            if ($this->hasIndex('project_likes', 'project_likes_project_index')) {
                $table->dropIndex('project_likes_project_index');
            }
        });

        Schema::table('project_bookmarks', function (Blueprint $table) {
            if ($this->hasIndex('project_bookmarks', 'project_bookmarks_project_index')) {
                $table->dropIndex('project_bookmarks_project_index');
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            if ($this->hasIndex('comments', 'comments_project_created_index')) {
                $table->dropIndex('comments_project_created_index');
            }
            if ($this->hasIndex('comments', 'comments_user_index')) {
                $table->dropIndex('comments_user_index');
            }
        });

        Schema::table('skill_endorsements', function (Blueprint $table) {
            if ($this->hasIndex('skill_endorsements', 'endorsements_project_skill_index')) {
                $table->dropIndex('endorsements_project_skill_index');
            }
            if ($this->hasIndex('skill_endorsements', 'endorsements_user_index')) {
                $table->dropIndex('endorsements_user_index');
            }
        });
    }
};
