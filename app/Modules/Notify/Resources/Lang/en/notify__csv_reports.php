<?php

use App\Modules\Notify\DB\Models\NotifyCsvReport;

return [
    'report_types' => [
        NotifyCsvReport::REPORT_TYPE_ZERO => 'Zero Report',
        NotifyCsvReport::REPORT_TYPE_ASSET => 'Asset report',
    ],
];