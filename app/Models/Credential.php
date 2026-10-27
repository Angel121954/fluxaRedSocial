<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credential extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'issuer',
        'type',
        'issued_at',
        'expires_at',
        'credential_id',
        'credential_url',
        'description',
        'privacy',
    ];

    protected $casts = [
        'issued_at' => 'date:Y-m-d',
        'expires_at' => 'date:Y-m-d',
    ];

    public const TYPES = [
        'certification' => 'Certificación',
        'course' => 'Curso',
        'competency' => 'Competencia',
        'award' => 'Reconocimiento',
        'other' => 'Otro',
    ];

    public const PRIVACY = [
        'public' => 'Público',
        'private' => 'Privado',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
