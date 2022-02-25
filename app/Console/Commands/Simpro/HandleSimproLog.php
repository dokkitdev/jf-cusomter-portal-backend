<?php

namespace App\Console\Commands\Simpro;

use App\Services\SimproLogService;
use Illuminate\Console\Command;

class HandleSimproLog extends Command
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
