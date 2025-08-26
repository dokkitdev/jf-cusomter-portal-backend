<?php

namespace App\Modules\Notify\Http\Resources\NotifyCsvReport;

use App\Http\Resources\BaseResourceCollection;

class NotifyCsvReportResourceCollection extends BaseResourceCollection
{
    public $collects = NotifyCsvReportResource::class;
}
