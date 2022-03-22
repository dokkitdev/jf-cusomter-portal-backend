<?php

namespace App\Console\Commands\Simpro;

use App\Services\AssetService;
use Illuminate\Console\Command;

class UpdateAssets extends Command
{
    protected $signature = 'simpro:update-assets';

    protected $description = 'Update Assets';

    public function handle(): void
    {
        $service = app(AssetService::class);

        $count = 0;

        $service->chunk(1000, function ($assets) use ($service, $count) {
            foreach ($assets as $asset) {
                $service->updateReportFields($asset);

                $count++;
            }

            $this->line($count);
        });

        $this->line('Assets updated');
    }
}
