<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationMedia extends Model
{
    protected $table = 'publication_media';

    protected $fillable = [
        'publication_id',
        'media_url',
        'type',
        'position'
    ];

    // Publicación a la que pertenece
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
