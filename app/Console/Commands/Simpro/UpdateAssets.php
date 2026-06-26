<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Models\Asset;
use App\Services\AssetService;

class UpdateAssets extends AbstractTimeoutCommand
{
    protected $signature = 'simpro:update-assets {--asset_id=0}';

    protected $description = 'Update Assets';

    protected $count = 0;

    public function handle(): void
    {
        $service = app(AssetService::class);

        if($this->option('asset_id')) {
            $asset = Asset::find($this->option('asset_id'));
            $service->updateReportFields($asset);
            return;
        }

        $service->chunk(1000, function ($assets) use ($service) {
            foreach ($assets as $asset) {
                $service->updateReportFields($asset);

                $this->count++;
            }

            $this->line($this->count);
        });

        $this->line('Assets updated');
    }
}
