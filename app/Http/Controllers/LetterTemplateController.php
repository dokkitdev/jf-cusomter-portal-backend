<?php

namespace App\Http\Controllers;

use App\Http\Requests\LetterTemplates\GetGroupedLetterTemplatesRequest;
use App\Http\Resources\GroupedLetterTemplatesResource;
use App\Services\LetterTemplateService;

class LetterTemplateController extends Controller
{
    public function getGroupedLetterTemplates(GetGroupedLetterTemplatesRequest $request): GroupedLetterTemplatesResource
    {
        return GroupedLetterTemplatesResource::make(LetterTemplateService::GROUPED_LETTER_TEMPLATES);
    }
}
