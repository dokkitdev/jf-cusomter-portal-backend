<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivateContracts\DownloadPrivateContractDocsRequest;
use App\Http\Requests\PrivateContracts\DownloadPrivateContractTemplateRequest;
use App\Http\Requests\PrivateContracts\GeneratePrivateContractLettersRequest;
use App\Http\Requests\PrivateContracts\SearchPrivateContractRequest;
use App\Http\Requests\PrivateContracts\UploadPrivateContractTemplateRequest;
use App\Services\PrivateContractService;
use Symfony\Component\HttpFoundation\Response;

class PrivateContractController extends Controller
{
    public function download(DownloadPrivateContractTemplateRequest $request, PrivateContractService $service, string $type): Response
    {
         return $service->downloadTemplate($type);
    }

    public function upload(UploadPrivateContractTemplateRequest $request, PrivateContractService $service, string $type): Response
    {
        $service->uploadTemplate($type, $request->file('template')->getContent());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function generateLetters(GeneratePrivateContractLettersRequest $request, PrivateContractService $service): Response
    {
        $service->generateLetters($request->onlyValidated('private_contract_ids'));

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function downloadDoc(DownloadPrivateContractDocsRequest $request, PrivateContractService $service): Response
    {
        return $service->downloadDocFile($request->input('filename'));
    }

    public function search(SearchPrivateContractRequest $request, PrivateContractService $service): Response
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
