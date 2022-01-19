<?php

namespace App\Http\Controllers;

use App\Http\Requests\Documents\CreateDocumentRequest;
use App\Http\Requests\Documents\DeleteDocumentRequest;
use App\Http\Requests\Documents\GetDocumentRequest;
use App\Http\Requests\Documents\SearchDocumentRequest;
use App\Http\Requests\Documents\UpdateDocumentRequest;
use App\Services\DocumentService;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function create(CreateDocumentRequest $request, DocumentService $service)
    {
        $result = $service->create($request->onlyValidated());

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function update(UpdateDocumentRequest $request, DocumentService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function delete(DeleteDocumentRequest $request, DocumentService $service, $id)
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function get(GetDocumentRequest $request, DocumentService $service, $id)
    {
        $result = $service
            ->with($request->onlyValidated('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchDocumentRequest $request, DocumentService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
