<?php

declare(strict_types = 1);

namespace Tests\Feature\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JuniorFontenele\LaravelEvents\Models\EventRegistry;
use JuniorFontenele\LaravelEvents\Tests\TestCase;

class EventsListCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testEventsListCommand(): void
    {
        EventRegistry::factory()->create([
            'name' => 'Test Event',
            'className' => 'App\Events\TestEvent',
            'type' => 'user',
            'level' => 'info',
            'description' => 'A test event',
        ]);

        $this->artisan('events:list')
            ->expectsTable(
                ['ID', 'Name', 'Class', 'Type', 'Level', 'Description'],
                [[1, 'Test Event', 'App\Events\TestEvent', 'User', 'Info', 'A test event']]
            )
            ->assertExitCode(0);
    }
}
