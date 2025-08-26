<?php

namespace App\Modules\Notify;

use App\Providers\BaseServiceProvider;

class NotifyModuleServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'notify');

        config(['filesystems.disks.letter_templates' => config('notify.storage.letter_templates')]);
        config(['filesystems.disks.pdf_reports' => config('notify.storage.pdf_reports')]);

        $this->loadTranslationsFrom(__DIR__ . '/Resources/Lang', 'notify');
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'notify');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/DB/Migrations');
    }

    public function register(): void
    {
    }
}
