<?php

namespace App\Http\Requests\Media;

use App\Http\Requests\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\MediaService;

class DownloadMediaRequest extends Request
{
    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(MediaService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Media']));
        }
    }
}
