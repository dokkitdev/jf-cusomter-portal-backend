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
        $schedule->command('simpro:handle-jobs job.created')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs job 5 0')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs job 5 1')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs job 5 2')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs job 5 3')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs job 5 4')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();

        $schedule->command('simpro:handle-jobs site')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs asset')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs company')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs individual')->environments(['production'])->everyMinute()->withoutOverlapping()->runInBackground();



        $jobs = SimproLog::LOGGABLE_TYPE_JOBS;
        $schedule->command("simpro:handle-log {$jobs}")->environments(['production'])->everyMinute()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();

        $sites = SimproLog::LOGGABLE_TYPE_SITES;
        $schedule->command("simpro:handle-log {$sites}")->environments(['production'])->everyMinute()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();

        $assets = SimproLog::LOGGABLE_TYPE_ASSETS;
        $schedule->command("simpro:handle-log {$assets} 5 0")->environments(['production'])->everyMinute()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 5 1")->environments(['production'])->everyMinute()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 5 2")->environments(['production'])->everyMinute()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 5 3")->environments(['production'])->everyMinute()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();
        $schedule->command("simpro:handle-log {$assets} 5 4")->environments(['production'])->everyMinute()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();

        $schedule->command("simpro:save-simpro-log {$assets}")->environments(['production'])->hourly()->skip($this->skipCriterias())->withoutOverlapping()->runInBackground();



        $schedule->command('clear:set-password-hash')->hourly()->withoutOverlapping()->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function skipCriterias()
    {
        return now()->isWeekday() && (now()->hour >= 6) && (now()->hour <= 19);
    }

}
