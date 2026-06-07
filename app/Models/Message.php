<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'read_at',
        'media_type',
        'media_url',
        'media_name',
        'media_size',
        'public_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'media_size' => 'integer',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isMedia(): bool
    {
        return $this->media_type !== null && $this->media_url !== null;
    }

    public function isImage(): bool
    {
        return $this->media_type === 'image';
    }

    public function isFile(): bool
    {
        return $this->media_type === 'file';
    }

    public function mediaIcon(): string
    {
        if (! $this->media_name) {
            return 'file';
        }

        $ext = strtolower(pathinfo($this->media_name, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => 'pdf',
            'zip', 'rar', '7z', 'tar', 'gz' => 'archive',
            'doc', 'docx' => 'word',
            'xls', 'xlsx', 'csv' => 'excel',
            'mp3', 'wav', 'ogg', 'flac' => 'audio',
            'mp4', 'mov', 'avi', 'mkv' => 'video',
            default => 'file',
        };
    }

    public function isGif(): bool
    {
        return $this->media_type === 'gif';
    }
}
