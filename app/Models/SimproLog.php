<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class SimproLog extends Model
{
    use ModelTrait;

    const HANDLE_STATUS_NEW = 'new';
    const HANDLE_STATUS_ERROR = 'error';

    const LOGGABLE_TYPE_SITES = 'sites';
    const LOGGABLE_TYPE_JOBS = 'jobs';

    protected $table = 'simpro_log';

    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'handle_status',
        'handle_result'
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'handle_result' => 'array',
    ];
}