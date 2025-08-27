<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\Http\Requests\ZeroReports\CreateZeroReportRequest;
use App\Modules\Notify\Services\ZeroReportService;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\Response;

class ZeroReportController extends Controller
{
    public function create(CreateZeroReportRequest $request, ZeroReportService $service): Response
    {
        $service->initReportGeneration(
            CarbonImmutable::parse($request->input('date_from')),
            CarbonImmutable::parse($request->input('date_to')),
        );

        return response('', Response::HTTP_NO_CONTENT);
    }
}
