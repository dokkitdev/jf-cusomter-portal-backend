<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected const DATE_FORMAT = 'Y-m-d\\TH:i:s.up';

    public static $wrap = null;
}
