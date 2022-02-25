<?php

namespace App\Http\Requests\Assets;

use App\Http\Requests\Request;

class GetAssetRequest extends Request
{
    public function rules(): array
    {
        $with = implode(',', [
            'site',
            'site.primary_site_contact',
            'site.customers',
            'site.customer',
            'asset_custom_fields',
            'asset_attachments',
            'asset_test_records',
            'asset_test_records.job',
            'asset_test_records.asset_test_record_readings',

            'asset_test_record',
            'job',
            'job_customer',
            'job.job_no_access_dates',
            'next_schedule'
        ]);

        return [
            'with' => 'array',
            'with.*' => "string|in:{$with}",
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), 'Asset');
    }
}
