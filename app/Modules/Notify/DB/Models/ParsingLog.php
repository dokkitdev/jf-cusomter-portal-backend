<?php

namespace App\Modules\Notify\DB\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Fields:
 *
 * @property int     id
 * @property string  parsing_type
 * @property Carbon  parsing_date
 * @property int     total_count
 * @property int     success_count
 * @property int[]   ids
 * @property array   error_reasons
 * @property ?Carbon created_at
 * @property ?Carbon updated_at
 *
 * Relations:
 */
class ParsingLog extends BaseModel
{
    public const PARSING_TYPE_WAREHOUSE_REPORT = 'warehouse_report';

    public const ERROR_REASON_WAREHOUSE_REPORT_UNKNOWN_JOB_STAGE = 'warehouse_report_unknown_job_stage';
    public const ERROR_REASON_WAREHOUSE_REPORT_UNKNOWN_JOB_TYPE = 'warehouse_report_unknown_job_type';

    protected $fillable = [
        'parsing_type',
        'parsing_date',
        'total_count',
        'success_count',
        'ids',
        'error_reasons',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'parsing_date' => 'date',
        'ids' => 'array',
        'error_reasons' => 'array',
    ];
}