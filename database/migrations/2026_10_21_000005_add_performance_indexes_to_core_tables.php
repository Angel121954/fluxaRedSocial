<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addUnique('users', ['email']);
        $this->addUnique('users', ['username']);
        $this->addUnique('profiles', ['user_id']);
        $this->addUnique('notification_preferences', ['user_id']);
        $this->addUnique('technologies', ['slug']);
        $this->addUnique('badges', ['slug']);
        $this->addUnique('follows', ['follower_id', 'followed_id']);
        $this->addUnique('project_likes', ['user_id', 'project_id']);
        $this->addUnique('project_bookmarks', ['user_id', 'project_id']);
        $this->addUnique('project_reports', ['user_id', 'project_id']);
        $this->addUnique('skill_endorsements', ['user_id', 'project_id', 'skill_type']);
        $this->addUnique('conversations', ['user_a_id', 'user_b_id']);
        $this->addUnique('user_badge', ['user_id', 'badge_id']);
        $this->addUnique('diary_responses', ['diary_id', 'user_id']);
        $this->addUnique('diary_response_likes', ['diary_response_id', 'user_id']);
        $this->addUnique('user_blocks', ['blocker_id', 'blocked_id']);

        $this->addIndex('comments', ['project_id', 'parent_id', 'created_at']);
        $this->addIndex('projects', ['user_id', 'created_at']);
        $this->addIndex('projects', ['privacy', 'created_at']);
        $this->addIndex('projects', ['privacy', 'likes_count']);
        $this->addIndex('messages', ['conversation_id', 'created_at']);
        $this->addIndex('messages', ['conversation_id', 'sender_id', 'read_at']);
        $this->addIndex('notifications', ['user_id', 'is_read', 'created_at']);
        $this->addIndex('notifications', ['user_id', 'created_at']);
        $this->addIndex('diary_responses', ['diary_id', 'created_at']);
        $this->addIndex('diary_responses', ['diary_id', 'likes_count']);
    }

    public function down(): void
    {
        $this->dropIndex('diary_responses', ['diary_id', 'likes_count']);
        $this->dropIndex('diary_responses', ['diary_id', 'created_at']);
        $this->dropIndex('notifications', ['user_id', 'created_at']);
        $this->dropIndex('notifications', ['user_id', 'is_read', 'created_at']);
        $this->dropIndex('messages', ['conversation_id', 'sender_id', 'read_at']);
        $this->dropIndex('messages', ['conversation_id', 'created_at']);
        $this->dropIndex('projects', ['privacy', 'likes_count']);
        $this->dropIndex('projects', ['privacy', 'created_at']);
        $this->dropIndex('projects', ['user_id', 'created_at']);
        $this->dropIndex('comments', ['project_id', 'parent_id', 'created_at']);

        $this->dropUnique('user_blocks', ['blocker_id', 'blocked_id']);
        $this->dropUnique('diary_response_likes', ['diary_response_id', 'user_id']);
        $this->dropUnique('diary_responses', ['diary_id', 'user_id']);
        $this->dropUnique('user_badge', ['user_id', 'badge_id']);
        $this->dropUnique('conversations', ['user_a_id', 'user_b_id']);
        $this->dropUnique('skill_endorsements', ['user_id', 'project_id', 'skill_type']);
        $this->dropUnique('project_reports', ['user_id', 'project_id']);
        $this->dropUnique('project_bookmarks', ['user_id', 'project_id']);
        $this->dropUnique('project_likes', ['user_id', 'project_id']);
        $this->dropUnique('follows', ['follower_id', 'followed_id']);
        $this->dropUnique('badges', ['slug']);
        $this->dropUnique('technologies', ['slug']);
        $this->dropUnique('notification_preferences', ['user_id']);
        $this->dropUnique('profiles', ['user_id']);
        $this->dropUnique('users', ['username']);
        $this->dropUnique('users', ['email']);
    }

    private function addUnique(string $table, array $columns): void
    {
        $this->addIndex($table, $columns, true);
    }

    private function addIndex(string $table, array $columns, bool $unique = false): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $columns, $unique ? 'unique' : null)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($columns, $unique) {
            $unique ? $table->unique($columns) : $table->index($columns);
        });
    }

    private function dropUnique(string $table, array $columns): void
    {
        $this->dropIndex($table, $columns, true);
    }

    private function dropIndex(string $table, array $columns, bool $unique = false): void
    {
        $name = $table.'_'.implode('_', $columns).($unique ? '_unique' : '_index');

        if (! Schema::hasTable($table) || ! Schema::hasIndex($table, $name)) {
            return;
        }

        try {
            Schema::table($table, fn (Blueprint $table) => $table->dropIndex($name));
        } catch (Throwable) {
        }
    }
};
