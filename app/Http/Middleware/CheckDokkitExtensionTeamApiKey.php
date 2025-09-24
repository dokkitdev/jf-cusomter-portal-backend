<?php

namespace App\Http\Middleware;

use App\Models\Team\SimProTeams;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CheckDokkitExtensionTeamApiKey
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
        $team_api_key = $request->header('x-team-api-key');

        if (!empty($team_api_key)) {
            $team = SimProTeams::where('api_key', $team_api_key)->first();

            if ($team) {
                $request->attributes->add(['auth_team' => $team]);

                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Invalid or missing Team API key'
        ], ResponseAlias::HTTP_FORBIDDEN);

    }
}
