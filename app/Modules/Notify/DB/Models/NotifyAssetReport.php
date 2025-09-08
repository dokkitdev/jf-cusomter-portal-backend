<?php

namespace App\Modules\Notify\DB\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Fields:
 *
 * @property int     id
 * @property int     site_id
 * @property int     asset_id
 * @property ?string uprn
 * @property ?string asset_type
 * @property ?string type
 * @property ?string fuel_type
 * @property ?string make
 * @property ?string model
 * @property ?Carbon last_service_date
 * @property ?Carbon service_level_start_date
 * @property ?Carbon job_due_date
 * @property ?Carbon next_service_date
 * @property ?string job_stage
 * @property ?string service_level_name
 * @property ?string last_mot_date
 * @property ?Carbon service_due
 * @property ?string next_scheduled_appointment_date
 * @property ?string no_access_visits
 * @property ?string location
 * @property ?string cancellation
 * @property ?Carbon created_at
 * @property ?Carbon updated_at
 *
 * Relations:
 */
class NotifyAssetReport extends BaseModel
{
    protected $fillable = [
        'site_id',
        'asset_id',
        'uprn',
        'asset_type',
        'type',
        'fuel_type',
        'make',
        'model',
        'last_service_date',
        'service_level_start_date',
        'job_due_date',
        'next_service_date',
        'job_stage',
        'service_level_name',
        'last_mot_date',
        'service_due',
        'next_scheduled_appointment_date',
        'no_access_visits',
        'location',
        'cancellation',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'last_service_date' => 'date',
        'service_level_start_date' => 'date',
        'job_due_date' => 'date',
        'next_service_date' => 'date',
        'service_due' => 'date',
    ];
}