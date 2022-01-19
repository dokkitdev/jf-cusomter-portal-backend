<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use ModelTrait;

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