<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Console\Commands;

use Illuminate\Console\Command;
use JuniorFontenele\LaravelEvents\Models\EventRegistry;

class EventsListCommand extends Command
{
    protected $signature = 'events:list';

    protected $description = 'List all events in the events database';

    public function handle(): void
    {
        $events = EventRegistry::all();

        $rows = [];

        foreach ($events as $event) {
            $rows[] = [
                $event->id,
                $event->name,
                $event->className,
                $event->type->getName(),
                $event->level->getName(),
                $event->description,
            ];
        }

        $this->table(
            ['ID', 'Name', 'Class', 'Type', 'Level', 'Description'],
            $rows,
        );
    }
}
