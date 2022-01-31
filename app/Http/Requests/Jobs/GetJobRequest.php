<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;

class GetJobRequest extends Request
{
    public function rules(): array
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:site,customer,recent_schedule,schedules,job_catalogs,job_attachments,job_work_orders',
            'with_count' => 'array',
            'with_count.*' => 'string|in:job_attachments'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), 'Job');

    }
}
