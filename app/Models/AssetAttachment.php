<?php

namespace App\Models;

class AssetAttachment extends BaseModel
{
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