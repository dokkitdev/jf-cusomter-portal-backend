<?php

namespace App\Modules\Notify\DB\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Fields:
 *
 * @property int     id
 * @property array   data
 * @property string  status
 * @property ?Carbon created_at
 * @property ?Carbon updated_at
 *
 * Relations:
 */
class NotifySimproWebhook extends BaseModel
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'data',
        'status',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'data' => 'array',
    ];
}