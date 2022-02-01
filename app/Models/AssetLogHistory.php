<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetLogHistory extends Model
{
    use ModelTrait;

    protected $fillable = [
        'assets_pulled_at',
        'assets_count'
    ];

    protected $hidden = ['pivot'];
}