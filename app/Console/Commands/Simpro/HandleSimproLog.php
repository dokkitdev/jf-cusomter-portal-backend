<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Services\SimproLogService;

class HandleSimproLog extends AbstractTimeoutCommand
{
    protected $signature = 'simpro:handle-log {type} {divider?} {mod?}';

    protected $description = 'Handle Simpro Log';

    public function handle(): void
    {
        $loggableType = $this->argument('type');
        $divider = $this->argument('divider');
        $mod = $this->argument('mod');

        app(SimproLogService::class)->handle($loggableType, $divider, $mod);

        $this->line('Simpro Log handled');
    }
}
