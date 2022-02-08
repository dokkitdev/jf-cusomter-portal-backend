<?php

namespace App\Http\Requests\Sites;

use App\Http\Requests\Request;

class GetSiteRequest extends Request
{
    public function rules(): array
    {
        return [
            'with' => 'array',
            'with.*' => "string|in:site_contacts,primary_site_contact,customers,customer",
            'with_count' => 'array',
            'with_count.*' => 'string|in:open_jobs'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), 'Site');
    }
}
