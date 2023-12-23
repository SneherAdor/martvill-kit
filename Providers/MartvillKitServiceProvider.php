<?php

namespace Modules\MartvillKit\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class MartvillKitServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        // 
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            \Modules\MartvillKit\Console\UpgradeGenerator::class,
            \Modules\MartvillKit\Console\GenerateInstaller::class,
        ]);
    }
}
