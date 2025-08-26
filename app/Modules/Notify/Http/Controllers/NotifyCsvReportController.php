<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\DB\Services\NotifyCsvReportService;
use App\Modules\Notify\Http\Requests\NotifyCsvReports\SearchNotifyCsvReportsRequest;
use App\Modules\Notify\Http\Resources\NotifyCsvReport\NotifyCsvReportResourceCollection;

class NotifyCsvReportController extends Controller
{
    public function search(SearchNotifyCsvReportsRequest $request, NotifyCsvReportService $service): NotifyCsvReportResourceCollection
    {
        $result = $service->search($request->onlyValidated());

        return NotifyCsvReportResourceCollection::make($result);
    }
}
