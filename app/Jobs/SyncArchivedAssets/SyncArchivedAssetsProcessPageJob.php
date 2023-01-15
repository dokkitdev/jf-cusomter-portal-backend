<?php

namespace App\Jobs\SyncArchivedAssets;

use App\Jobs\AbstractJob;

class SyncArchivedAssetsProcessPageJob extends AbstractJob
{
    protected int $page;

    public function __construct(int $page)
    {
        $this->page = $page;
    }

    public function handle()
    {
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
