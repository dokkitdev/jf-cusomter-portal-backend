<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\DB\Services\NotifyAssetReportValidationService;
use App\Modules\Notify\Http\Requests\AssetReportValidations\SearchAssetReportValidationsRequest;
use App\Modules\Notify\Http\Resources\AssetReportValidation\AssetReportValidationResourceCollection;

class AssetReportValidationController extends Controller
{
    public function search(SearchAssetReportValidationsRequest $request, NotifyAssetReportValidationService $service): AssetReportValidationResourceCollection
    {
        $result = $service->search($request->onlyValidated());

        return AssetReportValidationResourceCollection::make($result);
    }
}
