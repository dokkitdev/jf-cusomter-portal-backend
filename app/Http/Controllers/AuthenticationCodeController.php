<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticationCodes\SendAuthenticationCodeRequest;
use App\Services\AuthenticationCodeService;
use Illuminate\Http\Response as LaravelResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationCodeController extends Controller
{
    public function send(SendAuthenticationCodeRequest $request, AuthenticationCodeService $service): LaravelResponse
    {
        $service->send($request->input('email'));

        return response()->noContent(Response::HTTP_ACCEPTED);
    }
}
