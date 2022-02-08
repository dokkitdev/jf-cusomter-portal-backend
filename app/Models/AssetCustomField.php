<?php

namespace App\Models;

class AssetCustomField extends BaseModel
{
    protected $fillable = [
        'asset_id',
        'simpro_custom_field_id',
        'name',
        'value',
    ];

    protected $hidden = ['pivot'];
}