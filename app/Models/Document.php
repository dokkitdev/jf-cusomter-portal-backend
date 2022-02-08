<?php

namespace App\Models;

class Document extends BaseModel
{
    protected $fillable = [
        'media_id',
        'title',
        'description',
    ];

    protected $hidden = ['pivot'];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}