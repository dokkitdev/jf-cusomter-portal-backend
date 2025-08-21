<?php

namespace App\Modules\Notify\Http\Requests\WarehouseReports;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Modules\Notify\DB\Models\NotifyReport;
use App\Modules\Notify\DB\Services\NotifyReportService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CreateWarehouseReportRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }

    public function rules(): array
    {
        return [
            'days_count' => 'required|int|gt:0|lt:5',
        ];
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->checkNoInProgressReports();
    }

    protected function checkNoInProgressReports(): void
    {
        $existsNotFinished = app(NotifyReportService::class)->existsNotFinishedByReportTypes([
            NotifyReport::REPORT_TYPE_WAREHOUSE_PROJECT,
            NotifyReport::REPORT_TYPE_WAREHOUSE_SERVICE,
        ]);

        if ($existsNotFinished) {
            throw new UnprocessableEntityHttpException(__('notify::validation.exists_in_progress_report'));
        }
    }
}
