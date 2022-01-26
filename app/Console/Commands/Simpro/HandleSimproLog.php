<?php

namespace App\Console\Commands\Simpro;

use App\Services\SimproLogService;
use Illuminate\Console\Command;

class HandleSimproLog extends Command
{
    protected $signature = 'simpro:handle-log {type}';

    protected $description = 'Handle Simpro Log';

    public function handle(): void
    {
        $loggableType = $this->argument('type');

        app(SimproLogService::class)->handle($loggableType);

        $this->line('Simpro Log handled');
    }
}
