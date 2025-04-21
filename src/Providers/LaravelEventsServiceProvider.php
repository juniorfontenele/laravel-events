<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelEventsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->mergeConfigFrom(
        //     __DIR__.'/../../config/config.php',
        //     'skeleton'
        // );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->publishes([
        //     __DIR__.'/../../config/config.php' => config_path('skeleton.php'),
        // ], 'config');
    }
}
