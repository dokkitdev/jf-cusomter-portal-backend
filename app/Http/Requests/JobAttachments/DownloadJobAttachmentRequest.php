<?php

namespace App\Http\Requests\JobAttachments;

use App\Http\Requests\Request;
use App\Services\JobAttachmentService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadJobAttachmentRequest extends Request
{
    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(JobAttachmentService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'JobAttachment']));
        }
    }
}
