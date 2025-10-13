<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Console\Commands;

use Illuminate\Console\Command;
use JuniorFontenele\LaravelEvents\Models\Event;

class ClearOldEventsCommand extends Command
{
    protected $signature = 'events:clear-old';

    protected $description = 'Clear old events';

    public function handle(): void
    {
        $maxDays = config('events.max_days', 180);

        $this->info('Deleting events older than ' . $maxDays . ' days. Before: ' . now()->subDays($maxDays));

        $query = Event::where('created_at', '<', now()->subDays($maxDays));

        $count = $query->count();

        $this->info('Found ' . $count . ' events to delete');

        $query->delete();

        $this->info($count . ' events deleted');
    }
}
