<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\DB\Models\NotifyAssetReportValidation;
use App\Modules\Notify\DB\Services\NotifyAssetReportValidationService;
use App\Modules\Notify\Http\Requests\AssetReportValidations\GetAssetReportValidationSearchFiltersRequest;
use App\Modules\Notify\Http\Requests\AssetReportValidations\SearchAssetReportValidationsRequest;
use App\Modules\Notify\Http\Resources\AssetReportValidation\AssetReportValidationResourceCollection;
use App\Modules\Notify\Http\Resources\AssetReportValidation\AssetReportValidationSearchFiltersResource;

class AssetReportValidationController extends Controller
{
    public function search(SearchAssetReportValidationsRequest $request, NotifyAssetReportValidationService $service): AssetReportValidationResourceCollection
    {
        $result = $service->search($request->onlyValidated());

        return AssetReportValidationResourceCollection::make($result);
    }

    public function getSearchFilters(
        GetAssetReportValidationSearchFiltersRequest $request,
        NotifyAssetReportValidationService $service
    ): AssetReportValidationSearchFiltersResource {
        $selectedSiteId = $request->input('site_id');

        return AssetReportValidationSearchFiltersResource::make([
            'site_ids' => $service->getFieldValues('site_id'),
            'service_level_names' => $service->getFieldValues('service_level_name', $selectedSiteId),
            'asset_types' => $service->getFieldValues('asset_type', $selectedSiteId),
            'job_stages' => $service->getFieldValues('job_stage', $selectedSiteId),
            'error_types' => NotifyAssetReportValidation::ERROR_TYPES,
        ]);
    }
}
