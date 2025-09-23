<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CheckDokkitExtensionApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $configApiKey = config('microservice.dokkit_extension.api_key');
        $requestApiKey = $request->header('x-api-key');

        if (empty($configApiKey) || $requestApiKey !== $configApiKey) {
            return response()->json([
                'message' => 'Unauthorized. Invalid or missing API key'
            ], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
