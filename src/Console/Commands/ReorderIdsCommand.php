<?php

declare(strict_types = 1);

namespace JuniorFontenele\LaravelEvents\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use JuniorFontenele\LaravelEvents\Models\EventRegistry;

use function Laravel\Prompts\confirm;

class ReorderIdsCommand extends Command
{
    protected $signature = 'events:reorder-ids {--force}';

    protected $description = 'Reorder event ids';

    public function handle(): void
    {
        if (! $this->option('force') && ! confirm('This will reorder the event ids. Are you sure you want to continue?', false)) {
            return;
        }

        $this->info('Reordering event ids');

        $events = EventRegistry::query()->orderBy('id')->get('id');

        $countEvents = $events->count();

        if (! $countEvents) {
            $this->info('No events found in database');

            return;
        }

        $this->info('Found ' . $events->count() . ' events in database');

        $countUpdated = 0;

        DB::beginTransaction();

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($events as $index => $event) {
                $newId = $index + 1;

                if ($event->id === $newId) {
                    continue;
                }

                $event->id = $newId;
                $event->save();

                $countUpdated++;
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error occurred while reordering event ids: ' . $e->getMessage());
        }

        $this->info('Reordered ' . $countUpdated . ' event ids');
        $this->info('Event ids reordered successfully');
    }
}
