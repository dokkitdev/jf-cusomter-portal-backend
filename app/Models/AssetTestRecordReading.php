<?php

namespace App\Models;

class AssetTestRecordReading extends BaseModel
{
    protected $fillable = [
        'asset_test_record_id',
        'name',
        'value',
    ];

    protected $hidden = ['pivot'];
}