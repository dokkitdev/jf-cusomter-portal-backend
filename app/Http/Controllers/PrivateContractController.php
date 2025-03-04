<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivateContracts\DownloadPrivateContractTemplateRequest;
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
}
