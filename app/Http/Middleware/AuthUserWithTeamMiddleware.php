<?php

namespace App\Http\Middleware;

use App\Models\Team\SimProTeams;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthUserWithTeamMiddleware
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
        $user = $request->user();

        if ($user && $user->team_id) {
            $team = SimProTeams::whereId($user->team_id)->first();

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
