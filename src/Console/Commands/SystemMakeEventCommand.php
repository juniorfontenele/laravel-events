<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Console\Commands;

use Illuminate\Foundation\Console\EventMakeCommand;
use Illuminate\Support\Str;
use JuniorFontenele\LaravelEvents\Enums\EventLevel;
use JuniorFontenele\LaravelEvents\Enums\EventType;
use JuniorFontenele\LaravelEvents\Models\EventRegistry;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SystemMakeEventCommand extends EventMakeCommand
{
    protected function replaceClass($stub, $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        $eventName = text('Enter the event name', Str::headline(class_basename($name)), Str::headline(class_basename($name)));
        $type = select('What type of event would you like to create?', EventType::toArray());
        $level = select('What level of event would you like to create?', EventLevel::toArray());
        $description = text('Enter the event description', 'No description provided');

        $event = EventRegistry::create([
            'name' => $eventName,
            'type' => $type,
            'level' => $level,
            'description' => $description,
            'className' => $this->qualifyClass($name),
        ]);

        $stub = str_replace('{{ id }}', (string) $event->id, $stub);
        $stub = str_replace('{{ name }}', (string) $eventName, $stub);
        $stub = str_replace('{{ type }}', (string) EventType::tryFrom($type)?->name, $stub);
        $stub = str_replace('{{ level }}', (string) EventLevel::tryFrom($level)?->name, $stub);
        $stub = str_replace('{{ description }}', $description, $stub);

        return $stub;
    }
}
