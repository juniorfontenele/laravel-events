<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JuniorFontenele\LaravelEvents\Models\EventRegistry;

use function Laravel\Prompts\confirm;

use ReflectionClass;

class SyncFromFileCommand extends Command
{
    protected $signature = 'events:sync-from-file {--force}';

    protected $description = 'Sync events from files to the events database';

    public function handle(): void
    {
        if (! $this->option('force') && ! confirm(
            'This will overwrite all events in the database. Are you sure you want to continue?',
            false
        )) {
            return;
        }

        EventRegistry::query()->delete();

        $eventsPath = app_path('Events');

        $eventsFiles = File::allFiles($eventsPath);

        $this->info('Found ' . count($eventsFiles) . ' events');

        $count = 0;

        foreach ($eventsFiles as $file) {
            $relativePath = str_replace(app_path('Events') . DIRECTORY_SEPARATOR, '', $file->getPathname());

            $namespace = str_replace('/', '\\', dirname($relativePath));

            $className = $file->getBasename('.php');

            $fullClassName = $namespace === '.' ? 'App\\Events\\' . $className : 'App\\Events\\' . $namespace . '\\' . $className;

            if (! class_exists($fullClassName)) {
                continue;
            }

            $eventClass = new ReflectionClass($fullClassName);

            if (! $eventClass->isSubclassOf('JuniorFontenele\LaravelEvents\BaseEvent')) {
                continue;
            }

            $properties = $eventClass->getDefaultProperties();

            EventRegistry::create([
                'id' => $properties['id'],
                'name' => $properties['name'] ?? Str::headline($eventClass->getShortName()),
                'className' => $fullClassName,
                'type' => $properties['type']->value,
                'level' => $properties['level']->value,
                'description' => $properties['description'],
                'shouldLog' => $properties['shouldLog'],
                'shouldWriteToDatabase' => $properties['shouldWriteToDatabase'],
            ]);

            $count++;
        }

        $this->info($count . ' events synced from files to the events database');
    }
}
