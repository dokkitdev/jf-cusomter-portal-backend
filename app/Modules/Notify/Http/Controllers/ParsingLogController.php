<?php

namespace App\Modules\Notify\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notify\DB\Services\ParsingLogService;
use App\Modules\Notify\Http\Requests\ParsingLogs\SearchParsingLogsRequest;
use App\Modules\Notify\Http\Resources\ParsingLog\ParsingLogResourceCollection;

class ParsingLogController extends Controller
{
    public function search(SearchParsingLogsRequest $request, ParsingLogService $service): ParsingLogResourceCollection
    {
        $result = $service->search($request->onlyValidated());

        return ParsingLogResourceCollection::make($result);
    }
}
