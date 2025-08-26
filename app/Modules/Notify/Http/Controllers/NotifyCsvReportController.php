<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\DB\Models\NotifyCsvReport;
use App\Modules\Notify\DB\Services\NotifyCsvReportService;
use App\Modules\Notify\Http\Requests\NotifyCsvReports\DownloadCsvReportRequest;
use App\Modules\Notify\Http\Requests\NotifyCsvReports\SearchNotifyCsvReportsRequest;
use App\Modules\Notify\Http\Resources\NotifyCsvReport\NotifyCsvReportResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class NotifyCsvReportController extends Controller
{
    public function search(SearchNotifyCsvReportsRequest $request, NotifyCsvReportService $service): NotifyCsvReportResourceCollection
    {
        $result = $service->search($request->onlyValidated());

        return NotifyCsvReportResourceCollection::make($result);
    }

    public function downloadCsvReport(DownloadCsvReportRequest $request, NotifyCsvReportService $service, int $id): Response
    {
        /** @var NotifyCsvReport $report */
        $report = $service->find($id);

        $filePath = $service->getCsvFilePath($id);

        return response()->file($filePath, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$report->file_name}\"",
        ]);
    }
}
