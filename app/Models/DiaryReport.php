<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryReport extends Model
{
    protected $fillable = [
        'user_id',
        'diary_response_id',
        'reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diaryResponse(): BelongsTo
    {
        return $this->belongsTo(DiaryResponse::class);
    }
}
