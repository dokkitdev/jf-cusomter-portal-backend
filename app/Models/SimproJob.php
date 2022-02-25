<?php

namespace App\Models;

class SimproJob extends BaseModel
{
    const HANDLE_STATUS_NEW = 'new';
    const HANDLE_STATUS_ERROR = 'error';

    protected $fillable = [
        'data',
        'handle_status',
        'handle_result',
        'simpro_entity_id'
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'data' => 'array',
        'handle_result' => 'array',
    ];
}