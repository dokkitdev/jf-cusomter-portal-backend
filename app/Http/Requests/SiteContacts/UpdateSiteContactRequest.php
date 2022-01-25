<?php

namespace App\Http\Requests\SiteContacts;

use App\Http\Requests\Request;
use App\Services\SiteContactService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateSiteContactRequest extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'string|max:255|nullable',
            'given_name' => 'string|max:255',
            'family_name' => 'string|max:255|nullable',
            'email' => 'string|max:255|nullable',
            'work_phone' => 'string|max:255|nullable',
            'cell_phone' => 'string|max:255|nullable',
            'position' => 'string|max:255|nullable',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $siteContact = app(SiteContactService::class)->find($this->route('id'));

        if (!$siteContact) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'SiteContact']));
        }

        $this->validateExistsByPermissions($siteContact['site_id'], 'Site');
    }
}
