<?php

namespace App\Modules\Notify\Http\Requests;

use App\Http\Requests\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class NotifySimproWebhookRequest extends Request
{
    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->verifySignature();
    }

    protected function verifySignature(): void
    {
        if (!config('notify.simpro.webhooks.verify_signature')) {
            return;
        }

        $webhookSecret = config('notify.simpro.webhooks.secret');
        $requestBody = $this->getContent();
        $signature = $this->header('X-Response-Signature');

        $isVerified = hash_equals($signature, hash_hmac('sha1', $requestBody, $webhookSecret));

        if (!$isVerified) {
            throw new BadRequestHttpException(__('notify::validation.webhook_signature_verification_failed'));
        }
    }
}