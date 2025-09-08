<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\Http\Requests\NotifySimproWebhookRequest;
use App\Modules\Notify\Services\SimproWebhooks\RegisterSimproWebhookAction;
use Symfony\Component\HttpFoundation\Response;

class NotifySimproWebhookController extends Controller
{
    public function registerWebhook(NotifySimproWebhookRequest $request, RegisterSimproWebhookAction $action): Response
    {
        $action->handle($request->all());

        return response('', Response::HTTP_NO_CONTENT);
    }
}
