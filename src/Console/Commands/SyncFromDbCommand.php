<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Console\Commands;

use JuniorFontenele\LaravelEvents\Models\EventRegistry;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

use ReflectionClass;

class SyncFromDbCommand extends Command
{
    protected $signature = 'events:sync-from-db {--force}';

    protected $description = 'Sync events from database to the events files';

    public function handle(): void
    {
        $stub = file_get_contents(base_path('stubs/event.stub'));

        if (! $this->option('force') && ! confirm('This will overwrite the existing event files. Are you sure you want to continue?', false)) {
            return;
        }

        $this->info('Syncing events from database to files');

        $events = EventRegistry::all();

        $this->info('Found ' . $events->count() . ' events in database');

        $countUpdated = 0;
        $countCreated = 0;

        foreach ($events as $event) {
            if (! class_exists($event->className)) {
                $filename = $this->createFile($event);
                $countCreated++;
                $this->info('Created ' . $filename);

                continue;
            }

            if ($filename = $this->updateFile($event)) {
                $countUpdated++;
                $this->info('Updated ' . $filename);

                continue;
            }
        }

        $this->info('Created ' . $countCreated . ' events');
        $this->info('Updated ' . $countUpdated . ' events');
        $this->info('Events synced successfully');
    }

    protected function createFile(EventRegistry $event): string
    {
        $stub = file_get_contents(base_path('stubs/event.stub'));

        /** @phpstan-ignore-next-line */
        $stub = str_replace('{{ namespace }}', $this->getNamespace($event->className), $stub);
        $stub = str_replace('{{ class }}', class_basename($event->className), $stub);
        $stub = str_replace('{{ id }}', (string) $event->id, $stub);
        $stub = str_replace('{{ name }}', (string) $event->name, $stub);
        $stub = str_replace('{{ type }}', (string) $event->type->name, $stub);
        $stub = str_replace('{{ level }}', (string) $event->level->name, $stub);
        $stub = str_replace('{{ description }}', $event->description, $stub);

        $filename = $this->getFilePathFromNamespace($event->className);

        file_put_contents($filename, $stub);

        return $filename;
    }

    protected function updateFile(EventRegistry $event): bool|string
    {
        /** @phpstan-ignore-next-line */
        $eventClass = new ReflectionClass($event->className);

        $needsUpdate = false;

        $properties = $eventClass->getDefaultProperties();

        /**
             * @var string $stub
             * @phpstan-ignore-next-line
            */
        $stub = file_get_contents($eventClass->getFileName());

        $id = $properties['id'];
        $name = $properties['name'];
        $type = $properties['type']->name;
        $level = $properties['level']->name;
        $description = $properties['description'];
        $shouldLog = $properties['shouldLog'];
        $shouldWriteToDatabase = $properties['shouldWriteToDatabase'];

        if ($id !== $event->id) {
            $needsUpdate = true;
            $stub = str_replace(
                "public int \$id = $id;",
                "public int \$id = $event->id;",
                $stub
            );
        }

        if ($name !== $event->name) {
            $needsUpdate = true;
            $stub = str_replace(
                "public string \$name = '$name';",
                "public string \$name = '$event->name';",
                $stub
            );
        }

        if ($type !== $event->type->name) {
            $needsUpdate = true;
            $stub = str_replace(
                "public EventType \$type = EventType::{$type};",
                "public EventType \$type = EventType::{$event->type->name};",
                $stub
            );
        }

        if ($level !== $event->level->name) {
            $needsUpdate = true;
            $stub = str_replace(
                "public EventLevel \$level = EventLevel::{$level};",
                "public EventLevel \$level = EventLevel::{$event->level->name};",
                $stub
            );
        }

        if ($description !== $event->description) {
            $needsUpdate = true;
            $stub = str_replace(
                "public string \$description = '$description';",
                "public string \$description = '$event->description';",
                $stub
            );
        }

        if ($shouldLog !== $event->shouldLog) {
            $stringShouldLog = $shouldLog ? 'true' : 'false';
            $stringEventShouldLog = $event->shouldLog ? 'true' : 'false';
            $needsUpdate = true;
            $stub = str_replace(
                "public bool \$shouldLog = $stringShouldLog;",
                "public bool \$shouldLog = $stringEventShouldLog;",
                $stub
            );
        }

        if ($shouldWriteToDatabase !== $event->shouldWriteToDatabase) {
            $stringShouldWriteToDatabase = $shouldWriteToDatabase ? 'true' : 'false';
            $stringEventShouldWriteToDatabase = $event->shouldWriteToDatabase ? 'true' : 'false';
            $needsUpdate = true;
            $stub = str_replace(
                "public bool \$shouldWriteToDatabase = $stringShouldWriteToDatabase;",
                "public bool \$shouldWriteToDatabase = $stringEventShouldWriteToDatabase;",
                $stub
            );
        }

        if ($needsUpdate) {
            /** @phpstan-ignore-next-line */
            file_put_contents($eventClass->getFileName(), $stub);

            return $eventClass->getFileName();
        }

        return false;
    }

    protected function getFilePathFromNamespace(string $namespace): string
    {
        $relativePath = str_replace('\\', '/', str_replace('App\\', '', $namespace));

        $relativePath .= '.php';

        $fullPath = app_path($relativePath);

        return $fullPath;
    }

    protected function getNamespace(string $className): string
    {
        $lastBackslashPos = strrpos($className, '\\');

        if ($lastBackslashPos !== false) {
            return substr($className, 0, $lastBackslashPos);
        }

        return '';
    }
}
