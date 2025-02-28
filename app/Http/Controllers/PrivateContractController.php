<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivateContracts\DownloadPrivateContractTemplateRequest;
use App\Http\Requests\PrivateContracts\UploadPrivateContractTemplateRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrivateContractController extends Controller
{
    public function download(DownloadPrivateContractTemplateRequest $request, string $type): Response
    {
         $filename = config("defaults.private_contract.templates.names.{$type}");

         return Storage::disk('templates')->response($filename);
    }

    public function upload(UploadPrivateContractTemplateRequest $request, string $type): Response
    {
        Storage::disk('templates')->put(
            config("defaults.private_contract.templates.names.{$type}"),
            $request->file('template')->getContent(),
        );

        return response('', Response::HTTP_NO_CONTENT);
    }
}
