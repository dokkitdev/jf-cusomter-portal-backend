<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\DB\Services\NotifyReportService;
use App\Modules\Notify\Http\Requests\NotifyReports\DownloadLettersPdfRequest;
use App\Modules\Notify\Http\Requests\NotifyReports\SearchNotifyReportsRequest;
use App\Modules\Notify\Http\Resources\NotifyReport\NotifyReportResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class NotifyReportController extends Controller
{
    public function search(SearchNotifyReportsRequest $request, NotifyReportService $service): NotifyReportResourceCollection
    {
        $result = $service->search($request->onlyValidated());

        return NotifyReportResourceCollection::make($result);
    }

    public function downloadLettersPdf(DownloadLettersPdfRequest $request, NotifyReportService $service, int $id): Response
    {
        $filePath = $service->getLettersPdfFilePath($id);

        return response()->file($filePath);
    }
}
