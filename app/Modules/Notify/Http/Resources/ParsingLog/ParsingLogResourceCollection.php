<?php

namespace App\Modules\Notify\Http\Resources\ParsingLog;

use App\Http\Resources\BaseResourceCollection;

class ParsingLogResourceCollection extends BaseResourceCollection
{
    public $collects = ParsingLogResource::class;
}
