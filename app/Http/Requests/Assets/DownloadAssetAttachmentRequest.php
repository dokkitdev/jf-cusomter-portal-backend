<?php

namespace App\Http\Requests\Assets;

use App\Http\Requests\Request;
use App\Services\AssetAttachmentService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadAssetAttachmentRequest extends Request
{
    public function validateResolved()
    {
        parent::validateResolved();

        $exists = app(AssetAttachmentService::class)->exists($this->route('id'));

        if (!$exists) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'AssetAttachment']));
        }
    }
}
