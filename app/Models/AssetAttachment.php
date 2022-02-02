<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetAttachment extends Model
{
    use ModelTrait;

    protected $fillable = [
        'asset_id',
        'simpro_attachment_id',
        'name',
    ];

    protected $hidden = ['pivot'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}