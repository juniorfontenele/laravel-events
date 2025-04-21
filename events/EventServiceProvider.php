<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents;

use App\Listeners\SendEventsToRabbitMQ;
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

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerCommands();
    }

    public function boot(): void
    {
        $this->setupEventsAliases();
        $this->setupEventsListeners();
        $this->setupEventsScheduler();
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

        Event::listen('App\\Events\\*', SendEventsToRabbitMQ::class);
    }

    protected function setupEventsScheduler(): void
    {
        Schedule::command('events:clear-old')
            ->dailyAt('03:05');
    }
}
