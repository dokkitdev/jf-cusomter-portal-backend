<?php

namespace App\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected const DATE_FORMAT = 'Y-m-d\\TH:i:s.u\\Z';

    public static $wrap = null;

    protected function renderDate(?CarbonInterface $date): ?string
    {
        return isset($date) ? $date->setTimezone('UTC')->format(self::DATE_FORMAT) : null;
    }
}
