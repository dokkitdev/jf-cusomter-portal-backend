<?php

namespace App\Console\Commands\Simpro;

use App\Services\AssetService;
use Illuminate\Console\Command;

class UpdateAssets extends Command
{
    protected $signature = 'simpro:update-assets';

    protected $description = 'Update Assets';

    protected $count = 0;

    public function handle(): void
    {
        $service = app(AssetService::class);

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
