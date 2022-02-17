<?php

namespace App\Http\Controllers;

use App\Exports\JobsExport;
use App\Exports\JobsReportExport;
use App\Http\Requests\Jobs\CreateInSimproJobRequest;
use App\Http\Requests\Jobs\GetCostCentersRequest;
use App\Http\Requests\Jobs\GetJobRequest;
use App\Http\Requests\Jobs\GetStatusesRequest;
use App\Http\Requests\Jobs\SearchJobRequest;
use App\Services\JobService;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    public function createInSimpro(CreateInSimproJobRequest $request, JobService $service)
    {
        $data = Arr::except($request->onlyValidated(), 'files');

        if ($request->has('files')) {
            $files = $request->allFiles();

            foreach ($files['files'] as $file) {
                $data['files'][] = [
                    'content' => file_get_contents($file->getPathname()),
                    'filename' => $file->getClientOriginalName()
                ];
            }
        }

        $result = $service->createInSimpro($data);

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function get(GetJobRequest $request, JobService $service, $id)
    {
        $result = $service
            ->with($request->onlyValidated('with', []))
            ->withCount($request->onlyValidated('with_count', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchJobRequest $request, JobService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function export(SearchJobRequest $request, JobService $service)
    {
        return Excel::download(new JobsExport($service, $request->onlyValidated()), 'jobs.csv');
    }

    public function exportReport(SearchJobRequest $request, JobService $service)
    {
        return Excel::download(new JobsReportExport($service, $request->onlyValidated()), 'jobs_report.csv');
    }

    public function getCostCenters(GetCostCentersRequest $request, JobService $service)
    {
        $result = $service->getCostCenters();

        return response()->json($result);
    }

    public function getStatuses(GetStatusesRequest $request, JobService $service)
    {
        $result = $service->getStatuses();

        return response()->json($result);
    }
}
