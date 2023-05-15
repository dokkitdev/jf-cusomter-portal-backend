<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractTimeoutCommand extends Command
{
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->registerTimeoutHandler();

        return parent::run($input, $output);
    }

    protected function registerTimeoutHandler()
    {
        $timeout = config('artisan.timeout_seconds');

        if ($timeout > 0) {
            pcntl_signal(SIGALRM, function () {
                posix_kill(getmypid(), SIGKILL);

                exit(1);
            });

            pcntl_alarm($timeout);
        }
    }
}
