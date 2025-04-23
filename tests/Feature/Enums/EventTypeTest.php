<?php

declare(strict_types = 1);

namespace Tests\Feature\Enums;

use JuniorFontenele\LaravelEvents\Enums\EventType;
use JuniorFontenele\LaravelEvents\Tests\TestCase;

class EventTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('User', EventType::USER->getName());
        $this->assertEquals('System', EventType::SYSTEM->getName());
    }

    public function testToArray(): void
    {
        $expected = [
            'user' => 'User',
            'system' => 'System',
        ];

        $this->assertEquals($expected, EventType::toArray());
    }
}
