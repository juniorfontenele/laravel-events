<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use JuniorFontenele\LaravelEvents\Console\Commands\ClearOldEventsCommand;
use JuniorFontenele\LaravelEvents\Console\Commands\EventsListCommand;
use JuniorFontenele\LaravelEvents\Console\Commands\ReorderIdsCommand;
use JuniorFontenele\LaravelEvents\Console\Commands\SyncFromDbCommand;
use JuniorFontenele\LaravelEvents\Console\Commands\SyncFromFileCommand;
use JuniorFontenele\LaravelEvents\Console\Commands\SystemMakeEventCommand;
use JuniorFontenele\LaravelEvents\Listeners\WriteEventsToDatabase;
use JuniorFontenele\LaravelEvents\Listeners\WriteEventsToLog;
use JuniorFontenele\LaravelEvents\Models\EventRegistry;

class LaravelEventsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerCommands();

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/events.php',
            'events'
        );

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function boot(): void
    {
        $this->setupEventsAliases();
        $this->setupEventsListeners();
        $this->setupEventsScheduler();

        $this->publishes([
            __DIR__ . '/../config/events.php' => config_path('events.php'),
        ], 'laravel-events-config');

        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'laravel-events-migrations');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncFromFileCommand::class,
                SyncFromDbCommand::class,
                ClearOldEventsCommand::class,
                SystemMakeEventCommand::class,
                EventsListCommand::class,
                ReorderIdsCommand::class,
            ]);
        }
    }

    protected function setupEventsAliases(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('EventRegistry', EventRegistry::class);
    }

    protected function setupEventsListeners(): void
    {
        Event::listen('App\\Events\\*', WriteEventsToLog::class);
        Event::listen('App\\Events\\*', WriteEventsToDatabase::class);
    }

    protected function setupEventsScheduler(): void
    {
        Schedule::command('events:clear-old')
            ->dailyAt('03:05');
    }
}
