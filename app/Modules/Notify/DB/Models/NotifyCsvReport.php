<?php

namespace App\Modules\Notify\DB\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Fields:
 *
 * @property int     id
 * @property string  report_type
 * @property string  file_name
 * @property bool    is_finished
 * @property ?Carbon created_at
 * @property ?Carbon updated_at
 *
 * Relations:
 */
class NotifyCsvReport extends BaseModel
{
    public const REPORT_TYPE_ZERO = 'zero';

    protected $fillable = [
        'report_type',
        'file_name',
        'is_finished',
    ];

    protected $hidden = ['pivot'];
}