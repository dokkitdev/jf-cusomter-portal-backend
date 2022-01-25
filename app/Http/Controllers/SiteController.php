<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sites\GetSiteRequest;
use App\Http\Requests\Sites\SearchSiteRequest;
use App\Http\Requests\Sites\UpdateSiteRequest;
use App\Services\SiteService;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends Controller
{
    public function get(GetSiteRequest $request, SiteService $service, $id)
    {
        $result = $service
            ->with($request->onlyValidated('with', []))
            ->withCount($request->onlyValidated('with_count', []))
            ->find($id);

        return response()->json($result);
    }

    public function update(UpdateSiteRequest $request, SiteService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchSiteRequest $request, SiteService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
