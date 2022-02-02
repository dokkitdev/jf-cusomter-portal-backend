<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetTestRecordReading extends Model
{
    use ModelTrait;

    protected $fillable = [
        'asset_test_record_id',
        'name',
        'value',
    ];

    protected $hidden = ['pivot'];
}