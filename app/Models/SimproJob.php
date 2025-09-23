<?php

namespace App\Models;

class SimproJob extends BaseModel
{
    const HANDLE_STATUS_NEW = 'new';
    const HANDLE_STATUS_ERROR = 'error';
    const HANDLE_STATUS_COMPLETED = 'completed';
    const HANDLE_STATUS_PROCESSED = 'processed';

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
