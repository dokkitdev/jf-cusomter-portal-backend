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
 * @property string  error_type
 * @property string  error_text
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
    public const ERROR_TYPE_LAST_SERVICE_OVER_14_MONTHS = 'last_service_over_14_months';
    public const ERROR_TYPE_SERVICE_DUE_IN_30_DAYS = 'service_due_in_30_days';
    public const ERROR_TYPE_SERVICE_DUE_TOMORROW = 'service_due_tomorrow';
    public const ERROR_TYPE_SERVICE_COMPLETE_OUTSIDE_DUE_DATE = 'service_complete_outside_dude_date';
    public const ERROR_TYPE_NO_UPRN = 'no_uprn';
    public const ERROR_TYPE_NO_FUEL_TYPE = 'no_fuel_type';
    public const ERROR_TYPE_NO_ASSET_MAKE = 'no_asset_make';
    public const ERROR_TYPE_NO_MODEL = 'no_model';

    protected $fillable = [
        'site_id',
        'asset_id',
        'asset_report_id',
        'error_type',
        'error_text',
        'uprn',
        'fuel_type',
        'asset_type',
        'service_level_name',
        'job_stage',
    ];

    protected $hidden = ['pivot'];
}