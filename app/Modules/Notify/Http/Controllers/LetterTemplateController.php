<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\Http\Requests\LetterTemplates\DownloadLetterTemplateRequest;
use App\Modules\Notify\Http\Requests\LetterTemplates\GetGroupedLetterTemplatesRequest;
use App\Modules\Notify\Http\Requests\LetterTemplates\UploadLetterTemplateRequest;
use App\Modules\Notify\Http\Resources\GroupedLetterTemplatesResource;
use App\Modules\Notify\Services\LetterTemplateService;
use Symfony\Component\HttpFoundation\Response;

class LetterTemplateController extends Controller
{
    public function getGroupedLetterTemplates(GetGroupedLetterTemplatesRequest $request): GroupedLetterTemplatesResource
    {
        return GroupedLetterTemplatesResource::make(LetterTemplateService::GROUPED_LETTER_TEMPLATES);
    }

    public function upload(UploadLetterTemplateRequest $request, LetterTemplateService $service, string $letterName): Response
    {
        $service->upload($letterName, $request->file('file'));

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function download(DownloadLetterTemplateRequest $request, LetterTemplateService $service, string $letterName): Response
    {
        $content = $service->getContent($letterName);

        return response($content, Response::HTTP_OK, [
            'Content-Disposition' => "attachment; filename=\"{$letterName}.docx\"",
        ]);
    }
}
