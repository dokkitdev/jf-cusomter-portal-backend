<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteContacts\CreateSiteContactRequest;
use App\Http\Requests\SiteContacts\DeleteSiteContactRequest;
use App\Http\Requests\SiteContacts\GetSiteContactRequest;
use App\Http\Requests\SiteContacts\UpdateSiteContactRequest;
use App\Services\SiteContactService;
use Symfony\Component\HttpFoundation\Response;

class SiteContactController extends Controller
{
    public function create(CreateSiteContactRequest $request, SiteContactService $service)
    {
        $result = $service->create($request->onlyValidated());

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function update(UpdateSiteContactRequest $request, SiteContactService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function delete(DeleteSiteContactRequest $request, SiteContactService $service, $id)
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function get(GetSiteContactRequest $request, SiteContactService $service, $id)
    {
        $result = $service->find($id);

        return response()->json($result);
    }
}
