<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assets\GetAssetRequest;
use App\Http\Requests\Assets\GetAssetServiceLevelsRequest;
use App\Http\Requests\Assets\SearchAssetRequest;
use App\Http\Requests\Assets\DownloadAssetAttachmentRequest;
use App\Services\AssetAttachmentService;
use App\Services\AssetService;
use Illuminate\Support\Facades\Storage;

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

    public function getServiceLevels(GetAssetServiceLevelsRequest $request, AssetService $service)
    {
        $result = $service->getAssetServiceLevels();

        return response()->json($result);
    }
}
