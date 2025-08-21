<?php

namespace App\Modules\Notify\Http\Resources\NotifyReport;

use App\Http\Resources\BaseResourceCollection;

class NotifyReportResourceCollection extends BaseResourceCollection
{
    public $collects = NotifyReportResource::class;
}
