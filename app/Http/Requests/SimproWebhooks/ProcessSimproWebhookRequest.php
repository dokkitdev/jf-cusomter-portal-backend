<?php

namespace App\Http\Requests\SimproWebhooks;

use App\Http\Requests\Request;
use App\Services\SimproWebhookService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProcessSimproWebhookRequest extends Request
{
    public function rules(): array
    {
        return [
            'ID' => 'required|string',
            'build' => 'string',
            'description' => 'string',
            'name' => 'string',
            'action' => 'string',
            'reference.companyID' => 'integer',
            'reference.jobID' => 'integer',
            'reference.testID' => 'integer',
            'reference.scheduleID' => 'integer',
            'reference.sectionID' => 'integer',
            'reference.costCenterID' => 'integer',
            'reference.siteID' => 'integer',
            'date_triggered' => 'string'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(SimproWebhookService::class);

        $body = $this->getContent();
        $header = $this->header('X-Response-Signature');

        if (!$service->isWebhookVerified($header, $body)) {
            throw new BadRequestHttpException(__('validation.exceptions.mismatched_hashes'));
        }
    }
}