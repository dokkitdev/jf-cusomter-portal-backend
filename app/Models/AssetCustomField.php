<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetCustomField extends Model
{
    use ModelTrait;

    protected $fillable = [
        'asset_id',
        'simpro_custom_field_id',
        'name',
        'value',
    ];

    protected $hidden = ['pivot'];
}