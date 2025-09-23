<?php

namespace App\Http\Controllers\DokkitExtension;

use App\Http\Controllers\Controller;
use App\Http\Requests\DokkitExtensionAddTeamRequest;
use App\Http\Requests\UpdateTokenRequest;
use App\Services\Parser\FullParserService;
use App\Services\SimProTeam\SimProTeamService;

class DokkitExtensionController extends Controller
{
    public function status()
    {
        return response()->json([
            'status' => 'ok',
            'service' => config('microservice.dokkit_extension.name'),
            'timestamp' => now()->toIso8601String()
        ]);
    }

    public function addTeam(DokkitExtensionAddTeamRequest $teamRequest)
    {
        $team = app(SimProTeamService::class)
            ->addSimProTeam($teamRequest->input('build_url'));

        if($teamRequest->input('token')){
            app(SimProTeamService::class)
                ->updateToken($team, $teamRequest->input('token'));
        }

        if($teamRequest->input('is_full_update')){
            app(FullParserService::class)->fullParse($team);
        }

        return response()->json([
            'message' => 'Team added',
            'data' => $team,
        ]);
    }

    public function updateToken(UpdateTokenRequest $tokenRequest)
    {
        return response()->json([
            'message' => 'Token updated',
            'data' => app(SimProTeamService::class)
                ->updateWithBuildUrl($tokenRequest->input('build_url'),
                    $tokenRequest->input('token')),
        ]);
    }
}
