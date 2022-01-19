<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class SimproJob extends Model
{
    use ModelTrait;

    const HANDLE_STATUS_NEW = 'new';
    const HANDLE_STATUS_ERROR = 'error';

    protected $fillable = [
        'data',
        'handle_status',
        'handle_result'
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'data' => 'array',
        'handle_result' => 'array',
    ];
}