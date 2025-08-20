<?php

namespace App\Modules\Notify;

use App\Providers\BaseServiceProvider;

class NotifyModuleServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'notify');

        config(['filesystems.disks.letter_templates' => config('notify.storage.letter_templates')]);

        $this->loadTranslationsFrom(__DIR__ . '/Lang', 'notify');

        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }

    public function register(): void
    {
    }
}
