<?php

use App\Modules\Notify\DB\Models\NotifyReport;

return [
    'report_types' => [
        NotifyReport::REPORT_TYPE_WAREHOUSE_PROJECT => 'Project Warehouse report',
        NotifyReport::REPORT_TYPE_WAREHOUSE_SERVICE => 'Service Warehouse report',
    ],
];