<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\Http\Requests\AssetReports\CreateAssetReportRequest;
use App\Modules\Notify\Services\AssetReportService;
use Symfony\Component\HttpFoundation\Response;

class AssetReportController extends Controller
{
    public function create(CreateAssetReportRequest $request, AssetReportService $service): Response
    {
        $service->initReportGeneration();

        return response('', Response::HTTP_NO_CONTENT);
    }
}
