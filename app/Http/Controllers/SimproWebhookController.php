<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimproWebhooks\ProcessSimproWebhookRequest;
use App\Services\SimproWebhookService;
use Symfony\Component\HttpFoundation\Response;

class SimproWebhookController extends Controller
{
    public function process(ProcessSimproWebhookRequest $request, SimproWebhookService $service)
    {
        $service->process($request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }
}