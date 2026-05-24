<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryResponseCommentLike extends Model
{
    protected $fillable = [
        'user_id',
        'diary_response_comment_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(DiaryResponseComment::class, 'diary_response_comment_id');
    }
}
