<?php

namespace App\Modules\Notify\Jobs;

use App\Jobs\AbstractJob;
use Carbon\CarbonImmutable;

class ZeroReportJob extends AbstractJob
{
    protected CarbonImmutable $dateFrom;
    protected CarbonImmutable $dateTo;
    protected int $csvReportId;

    public function __construct(CarbonImmutable $dateFrom, CarbonImmutable $dateTo, int $csvReportId)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->csvReportId = $csvReportId;

        $this->onQueue('notify:zero_report');
    }

    public function handle()
    {
    }

    public function failed(): void
    {
    }
    
    public function getDateFrom(): CarbonImmutable
    {
        return $this->dateFrom;
    }

    public function getDateTo(): CarbonImmutable
    {
        return $this->dateTo;
    }

    public function getCsvReportId(): int
    {
        return $this->csvReportId;
    }
}
