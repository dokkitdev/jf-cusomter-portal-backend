<?php

namespace App\Models;

class AssetLogHistory extends BaseModel
{
    protected $fillable = [
        'assets_pulled_at',
        'assets_count'
    ];

    protected $hidden = ['pivot'];
}