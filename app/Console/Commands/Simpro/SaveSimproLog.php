<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Services\SimproLogService;

class SaveSimproLog extends AbstractTimeoutCommand
{
    protected $signature = 'simpro:save-simpro-log {type}';

    protected $description = 'Save Simpro Log';

    public function handle(): void
    {
        $loggableType = $this->argument('type');

        app(SimproLogService::class)->saveAll($loggableType);

        $this->line('Simpro Log saved');
    }
}
