<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryResponseBookmark extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'diary_response_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(DiaryResponse::class, 'diary_response_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
