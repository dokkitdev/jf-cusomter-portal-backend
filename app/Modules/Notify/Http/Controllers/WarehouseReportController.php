<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\Http\Requests\WarehouseReports\CreateWarehouseReportRequest;
use App\Modules\Notify\Services\WarehouseReportService;
use Symfony\Component\HttpFoundation\Response;

class WarehouseReportController extends Controller
{
    public function create(CreateWarehouseReportRequest $request, WarehouseReportService $service): Response
    {
        $service->initReportGeneration($request->input('days_count'));

        return response('', Response::HTTP_NO_CONTENT);
    }
}
