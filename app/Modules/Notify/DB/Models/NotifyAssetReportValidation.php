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
 * @property int     asset_report_id
 * @property string  error
 * @property ?string uprn
 * @property ?string fuel_type
 * @property ?string asset_type
 * @property ?string service_level_name
 * @property ?string job_stage
 * @property ?Carbon created_at
 * @property ?Carbon updated_at
 *
 * Relations:
 */
class NotifyAssetReportValidation extends BaseModel
{
    protected $fillable = [
        'site_id',
        'asset_id',
        'asset_report_id',
        'uprn',
        'fuel_type',
        'asset_type',
        'service_level_name',
        'job_stage',
        'error',
    ];

    protected $hidden = ['pivot'];
}