<?php

namespace App\Jobs\SyncArchivedAssets;

use App\Jobs\AbstractJob;
use App\LogicServices\SyncArchivedAssetsLogicService;

class SyncArchivedAssetsProcessPageJob extends AbstractJob
{
    public $queue = 'sync_archived_assets:process_page';

    protected int $page;

    public function __construct(int $page)
    {
        $this->page = $page;
    }

    public function handle()
    {
        app(SyncArchivedAssetsLogicService::class)->processPage($this->getPage());
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
