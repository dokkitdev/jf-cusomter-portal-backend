<?php

namespace App\Modules\Notify\DB\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Fields:
 *
 * @property int     id
 * @property string  report_type
 * @property int     letters_generated
 * @property bool    is_finished
 * @property ?Carbon created_at
 * @property ?Carbon updated_at
 *
 * Relations:
 */
class NotifyReport extends BaseModel
{
    protected $fillable = [
        'report_type',
        'letters_generated',
        'is_finished',
    ];

    protected $hidden = ['pivot'];
}