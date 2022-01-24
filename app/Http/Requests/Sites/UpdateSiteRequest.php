<?php

namespace App\Http\Requests\Sites;

use App\Http\Requests\Request;
use App\Services\SiteContactService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UpdateSiteRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'string',
            'uprn' => 'string|nullable',
            'address' => 'string|nullable',
            'postal_code' => 'string|nullable',
            'city' => 'string|nullable',
            'country' => 'string|nullable',
            'county' => 'string|nullable',
            'primary_site_contact_id' => 'integer',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), 'Site');

        if ($this->has('primary_site_contact_id')) {
            $this->checkPrimarySiteContact();
        }
    }

    protected function checkPrimarySiteContact()
    {
        $service = app(SiteContactService::class);

        $exists = $service->exists([
            'id' => $this->get('primary_site_contact_id'),
            'site_id' => $this->route('id'),
        ]);

        if (!$exists) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.not_found', ['entity' => 'SiteContact']));
        }
    }
}
