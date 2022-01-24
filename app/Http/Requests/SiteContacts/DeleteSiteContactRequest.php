<?php

namespace App\Http\Requests\SiteContacts;

use App\Http\Requests\Request;
use App\Services\SiteContactService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteSiteContactRequest extends Request
{
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
