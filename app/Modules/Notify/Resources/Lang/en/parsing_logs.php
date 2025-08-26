<?php

use App\Modules\Notify\DB\Models\ParsingLog;

return [
    'parsing_types' => [
        ParsingLog::PARSING_TYPE_WAREHOUSE_REPORT => 'Warehouse',
    ],

    'error_reasons' => [
        ParsingLog::ERROR_REASON_WAREHOUSE_REPORT_UNKNOWN_JOB_STAGE => 'Job ":job_id". Stage is not Pending or Progress',
        ParsingLog::ERROR_REASON_WAREHOUSE_REPORT_UNKNOWN_JOB_TYPE => 'Job ":job_id". Type is not Project or Service',
    ],
];