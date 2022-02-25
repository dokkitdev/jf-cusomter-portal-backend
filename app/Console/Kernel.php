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

        $assets = SimproLog::LOGGABLE_TYPE_ASSETS;
        $schedule->command("simpro:handle-log {$assets} 10 0")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 1")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 2")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 3")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 4")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 5")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 6")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 7")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 8")->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 10 9")->everyMinute()->withoutOverlapping()->runInBackground();

        $schedule->command('simpro:handle-jobs job')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs site')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs asset')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs company')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs individual')->everyMinute()->withoutOverlapping()->runInBackground();

        $schedule->command("simpro:save-simpro-log {$assets}")->hourly()->withoutOverlapping()->runInBackground();

        $schedule->command('clear:set-password-hash')->hourly()->withoutOverlapping()->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
