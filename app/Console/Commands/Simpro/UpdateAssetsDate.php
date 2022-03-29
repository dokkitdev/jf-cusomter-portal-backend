<?php

namespace App\Console\Commands\Simpro;

use App\Services\AssetService;
use Illuminate\Console\Command;

class UpdateAssetsDate extends Command
{
    protected $signature = 'simpro:update-assets-date';

    protected $description = 'Update Assets Sortable Date field';

    protected $count = 0;

    public function handle(): void
    {
        $service = app(AssetService::class);

        $service->chunk(1000, function ($assets) use ($service) {
            foreach ($assets as $asset) {
                $service->update($asset['id'], [
                    'sortable_date' => $asset['last_test_date'] ?? $asset['last_cp12_date']
                ]);

                $this->count++;
            }

            $this->line($this->count);
        });

        $this->line('Assets Date updated');
    }
}
