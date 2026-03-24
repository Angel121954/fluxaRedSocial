<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    protected $table = 'educations';
    protected $fillable = [
        'user_id',
        'institution',
        'degree',
        'field',
        'graduated_year',
        'current',
    ];

    protected $casts = [
        'current'        => 'boolean',
        'graduated_year' => 'integer',
    ];

    // ── Relaciones ──────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
