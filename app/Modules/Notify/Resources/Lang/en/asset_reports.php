<?php

use App\Modules\Notify\DB\Models\NotifyAssetReportValidation;

return [
    'validation_messages' => [
        NotifyAssetReportValidation::ERROR_TYPE_LAST_SERVICE_OVER_14_MONTHS => 'Last service :diff days ago',
        NotifyAssetReportValidation::ERROR_TYPE_SERVICE_DUE_TOMORROW => 'Service due tomorrow',
        NotifyAssetReportValidation::ERROR_TYPE_SERVICE_DUE_IN_30_DAYS => 'Service due within :diff days',
        NotifyAssetReportValidation::ERROR_TYPE_SERVICE_COMPLETE_OUTSIDE_DUE_DATE => 'Service complete outside of due date :months months :days days',
        NotifyAssetReportValidation::ERROR_TYPE_NO_UPRN => 'No UPRN',
        NotifyAssetReportValidation::ERROR_TYPE_NO_FUEL_TYPE => 'No Fuel Type found',
        NotifyAssetReportValidation::ERROR_TYPE_NO_ASSET_MAKE => 'No Asset Make found',
        NotifyAssetReportValidation::ERROR_TYPE_NO_MODEL => 'No Model found',
    ],
];