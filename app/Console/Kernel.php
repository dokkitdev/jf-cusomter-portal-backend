<?php

namespace App\Console;

use App\Models\SimproLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule)
    {
        $jobs = SimproLog::LOGGABLE_TYPE_JOBS;
        $schedule->command("simpro:handle-log {$jobs}")->everyMinute()->withoutOverlapping()->runInBackground();

        $sites = SimproLog::LOGGABLE_TYPE_SITES;
        $schedule->command("simpro:handle-log {$sites}")->everyMinute()->withoutOverlapping()->runInBackground();

        $schedule->command('clear:set-password-hash')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
