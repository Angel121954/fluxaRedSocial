<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'content',
        'privacy',
        'parent_id',
        'likes_count',
        'comments_count',
        'shares_count'
    ];

    // Usuario que creó la publicación
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Imágenes o videos de la publicación
    public function media()
    {
        return $this->hasMany(PublicationMedia::class);
    }

    // Respuestas o publicaciones hijas
    public function replies()
    {
        return $this->hasMany(Publication::class, 'parent_id');
    }

    // Publicación padre (si es una respuesta)
    public function parent()
    {
        return $this->belongsTo(Publication::class, 'parent_id');
    }
}
