<?php

namespace App\Modules\Notify\Http\Requests\NotifyReports;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Modules\Notify\DB\Services\NotifyReportService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadLettersPdfRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->checkReportExistsAndFinished();
    }

    protected function checkReportExistsAndFinished(): void
    {
        $exists = app(NotifyReportService::class)->existsFinishedById($this->route('id'));

        if (!$exists) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'NotifyReportLetters']));
        }
    }
}
