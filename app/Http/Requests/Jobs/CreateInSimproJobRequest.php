<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;

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
    }
}
