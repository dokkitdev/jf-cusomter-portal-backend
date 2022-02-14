<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;
use App\Services\SiteService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CreateInSimproJobRequest extends Request
{
    public function rules(): array
    {
        $types = implode(',', config('defaults.permitted_media_types'));

        return [
            'site_id' => 'required|integer',
            'description' => 'string',
            'files' => 'array',
            'files.*' => "file|required|max:10240|mimes:{$types}"
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->get('site_id'), 'Site');

        $this->validateSiteCustomer();
    }

    protected function validateSiteCustomer()
    {
        $site = app(SiteService::class)->first($this->get('site_id'));

        if (!$site['customer_id']) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.not_found', ['entity' => 'Customer']));
        }
    }
}
