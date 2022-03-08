<?php

namespace App\Http\Controllers;

use App\Exports\AssetsReportExport;
use App\Exports\AssetsExport;
use App\Http\Requests\Assets\GetAssetNamesRequest;
use App\Http\Requests\Assets\GetAssetRequest;
use App\Http\Requests\Assets\GetAssetServiceLevelsRequest;
use App\Http\Requests\Assets\GetAssetTypesRequest;
use App\Http\Requests\Assets\SearchAssetRequest;
use App\Http\Requests\Assets\DownloadAssetAttachmentRequest;
use App\Services\AssetAttachmentService;
use App\Services\AssetService;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    public function get(GetAssetRequest $request, AssetService $service, $id)
    {
        $result = $service
            ->with($request->onlyValidated('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function download(DownloadAssetAttachmentRequest $request, AssetAttachmentService $service, $id)
    {
        $attachment = $service->download($id);

        return Storage::response($attachment['simpro_attachment_id'], $attachment['name']);
    }

    public function search(SearchAssetRequest $request, AssetService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function export(SearchAssetRequest $request, AssetService $service)
    {
        return Excel::download(new AssetsExport($service, $request->onlyValidated()), 'assets.csv');
    }

    public function exportReport(SearchAssetRequest $request, AssetService $service)
    {
        return Excel::download(new AssetsReportExport($service, $request->onlyValidated()), 'assets_report.csv');
    }

    public function getServiceLevels(GetAssetServiceLevelsRequest $request, AssetService $service)
    {
        $result = $service->getAssetServiceLevels();

        return response()->json($result);
    }

    public function getTypes(GetAssetTypesRequest $request, AssetService $service)
    {
        $result = $service->getAssetTypes();

        return response()->json($result);
    }

    public function getNames(GetAssetNamesRequest $request, AssetService $service)
    {
        $result = $service->getAssetNames();

        return response()->json($result);
    }
}
